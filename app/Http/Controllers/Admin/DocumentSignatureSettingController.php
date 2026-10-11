<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\UpsertDocumentSignatureRequest;
use App\Models\DocumentSignatureSetting;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class DocumentSignatureSettingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:attendance.document_signature.manage'),
        ];
    }

    public function index(): View
    {
        $settings = DocumentSignatureSetting::all()->keyBy('document_type');

        $employees = Employee::query()
            ->where('is_active', true)
            ->with('currentStatus.position')
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'signature_image'])
            ->map(fn ($e) => [
                'name' => $e->full_name,
                'title' => $e->currentStatus?->position?->position_name ?? '',
                'has_signature' => ! empty($e->getRawOriginal('signature_image')),
            ]);

        return view('attendance.document-signature.index', [
            'settings' => $settings,
            'documentTypes' => config('documents.signature_types'),
            'employees' => $employees,
        ]);
    }

    public function upsert(UpsertDocumentSignatureRequest $request): RedirectResponse
    {
        foreach ($request->validated('signers') as $type => $data) {
            DocumentSignatureSetting::updateOrCreate(
                ['document_type' => $type],
                ['signer_name' => $data['signer_name'], 'signer_title' => $data['signer_title']]
            );
        }

        return redirect()->route('attendance.document-signature.index')
            ->with('success', 'Pengaturan tanda tangan berhasil disimpan.')
            ->with('active_type', $request->input('_active_type'));
    }
}
