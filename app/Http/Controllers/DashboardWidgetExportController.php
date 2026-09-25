<?php

namespace App\Http\Controllers;

use App\Models\DashboardWidgetConfig;
use App\Models\User;
use App\Services\Dashboard\DashboardWidgetService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardWidgetExportController extends Controller
{
    // Widgets with dedicated PDF templates — bypass flat getExportData() for PDF
    private const DEDICATED_PDF = ['financial_hub', 'member_growth', 'ranking_program', 'comparable'];

    public function __construct(
        private readonly DashboardWidgetService $widgetService,
    ) {}

    public function export(string $widgetKey, string $format): StreamedResponse|Response
    {
        if (! in_array($format, ['csv', 'pdf'])) {
            abort(400, 'Format tidak didukung. Gunakan csv atau pdf.');
        }

        if (! $this->widgetService->isExportable($widgetKey)) {
            abort(403, 'Widget ini tidak dapat diekspor.');
        }

        /** @var User $user */
        $user = auth()->user();
        $currentStatus = $user->employee?->currentStatus;
        $positionId = $currentStatus?->position_id;

        if (! $positionId) {
            abort(403, 'Posisi tidak ditemukan.');
        }

        $allowed = DashboardWidgetConfig::where('position_id', $positionId)
            ->where('widget_key', $widgetKey)
            ->where('is_enabled', true)
            ->exists();

        if (! $allowed) {
            abort(403, 'Widget ini tidak tersedia untuk posisi Anda.');
        }

        $filename = "{$widgetKey}_".now()->format('Y-m-d');

        // Dedicated PDF widgets build their own rich data — bypass flat export path
        if ($format === 'pdf' && in_array($widgetKey, self::DEDICATED_PDF)) {
            return match ($widgetKey) {
                'financial_hub' => $this->exportFinancialHubPdf($filename),
                'comparable'    => $this->exportComparablePdf($filename),
                default => $this->exportMemberAnalyticsPdf($filename),
            };
        }

        $filters = [
            'branch_id' => request('branch_id', $user->employee?->branch_id),
            'month' => request('month', now()->month),
            'year' => request('year', now()->year),
            'area_id' => request('area_id'),
            'scope' => request('scope', 'cabang'),
        ];

        $data = $this->widgetService->getWidgetExportData($widgetKey, $filters);

        if (empty($data)) {
            abort(404, 'Tidak ada data untuk diekspor.');
        }

        $label = $this->widgetService->getLabel($widgetKey);

        return $format === 'csv'
            ? $this->exportCsv($data, $widgetKey, $label)
            : $this->exportGenericPdf($data, $widgetKey, $label);
    }

    private function exportCsv(array $data, string $widgetKey, string $label): StreamedResponse
    {
        $filename = "{$widgetKey}_".now()->format('Y-m-d').'.csv';
        $headers = array_keys($data[0]);

        return response()->streamDownload(function () use ($data, $headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($data as $row) {
                fputcsv($handle, array_values($row));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function exportGenericPdf(array $data, string $widgetKey, string $label): Response
    {
        $filename = "{$widgetKey}_".now()->format('Y-m-d');
        $headers = array_keys($data[0]);

        $pdf = Pdf::loadView('dashboard.widgets.export-pdf', [
            'title' => $label,
            'headers' => $headers,
            'rows' => $data,
            'exportedAt' => now()->format('d F Y H:i'),
        ]);

        return $pdf->download("{$filename}.pdf");
    }

    private function exportFinancialHubPdf(string $filename): Response
    {
        /** @var User $user */
        $user = auth()->user();
        $filters = [
            'month' => request('month', now()->month),
            'year' => request('year', now()->year),
            'area_id' => request('area_id'),
            'scope' => 'nasional',
        ];

        $data = $this->widgetService->getWidgetData('financial_hub', $filters);
        $docCode = 'DOC/FIN-HUB/'.($filters['year']).str_pad($filters['month'], 2, '0', STR_PAD_LEFT).'-'.strtoupper(substr(md5($user->id.now()->timestamp), 0, 6));

        $logoPath = public_path('img/logo/mindsia-logo.png');
        $logoSrc = file_exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('dashboard.widgets.financial_hub_export_pdf', [
            'data' => $data,
            'docCode' => $docCode,
            'logoSrc' => $logoSrc,
            'printedAt' => now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i').' WIB',
        ])->setPaper('a4', 'portrait')
            ->setOption('margin_top', 20)
            ->setOption('margin_right', 18)
            ->setOption('margin_bottom', 18)
            ->setOption('margin_left', 18);

        return $pdf->download("{$filename}.pdf");
    }

    private function exportMemberAnalyticsPdf(string $filename): Response
    {
        /** @var User $user */
        $user = auth()->user();
        $month = (int) request('month', now()->month);
        $year = (int) request('year', now()->year);

        $filters = [
            'branch_id' => request('branch_id', $user->employee?->branch_id),
            'month' => $month,
            'year' => $year,
        ];

        $data = $this->widgetService->getWidgetData('member_growth', $filters);

        $docCode = 'DOC/MBR-ANA/'.$year.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.strtoupper(substr(md5($user->id.now()->timestamp), 0, 6));
        $logoPath = public_path('img/logo/mindsia-logo.png');
        $logoSrc = file_exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('dashboard.widgets.member_program_analytics_export_pdf', [
            'data' => $data,
            'docCode' => $docCode,
            'logoSrc' => $logoSrc,
            'printedAt' => now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i').' WIB',
        ])->setPaper('a4', 'portrait')
            ->setOption('margin_top', 20)
            ->setOption('margin_right', 18)
            ->setOption('margin_bottom', 18)
            ->setOption('margin_left', 18);

        return $pdf->download("{$filename}.pdf");
    }

    private function exportComparablePdf(string $filename): Response
    {
        $filters = [
            'branch_a' => request('comparable_branch_a'),
            'branch_b' => request('comparable_branch_b'),
            'month_start' => (int) request('comparable_month_start', now()->month),
            'month_end' => (int) request('comparable_month_end', now()->month),
            'year' => (int) request('comparable_year', now()->year),
        ];

        $data = $this->widgetService->getWidgetData('comparable', $filters);

        $pdf = Pdf::loadView('dashboard.widgets.comparable_export_pdf', [
            'data' => $data,
        ])->setPaper('a4', 'portrait')
            ->setOption('margin_top', 20)
            ->setOption('margin_right', 18)
            ->setOption('margin_bottom', 18)
            ->setOption('margin_left', 18);

        return $pdf->download("{$filename}.pdf");
    }
}

