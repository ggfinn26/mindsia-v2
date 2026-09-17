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

        $branchId = $user->employee?->branch_id;
        $data = $this->widgetService->getWidgetExportData($widgetKey, $branchId);

        if (empty($data)) {
            abort(404, 'Tidak ada data untuk diekspor.');
        }

        $label = $this->widgetService->getLabel($widgetKey);

        return $format === 'csv'
            ? $this->exportCsv($data, $widgetKey, $label)
            : $this->exportPdf($data, $widgetKey, $label);
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

    private function exportPdf(array $data, string $widgetKey, string $label): Response
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
}
