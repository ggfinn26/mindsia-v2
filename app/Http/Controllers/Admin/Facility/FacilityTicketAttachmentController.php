<?php

namespace App\Http\Controllers\Admin\Facility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Facility\StoreFacilityTicketAttachmentRequest;
use App\Models\FacilityTicket;
use App\Models\FacilityTicketAttachment;
use App\Services\TelegramStorageService;
use Illuminate\Http\RedirectResponse;

class FacilityTicketAttachmentController extends Controller
{
    public function __construct(
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    public function store(StoreFacilityTicketAttachmentRequest $request, FacilityTicket $facilityTicket): RedirectResponse
    {
        $file = $request->file('file');
        $uploaded = $this->telegramStorage->uploadFile(
            $file->getRealPath(),
            $file->getClientOriginalName(),
            'facility_ticket_attachment',
            $facilityTicket->id,
        );

        $facilityTicket->attachments()->create([
            'telegram_file_id' => $uploaded['file_id'],
            'storage_path' => $uploaded['file_id'],
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by_employee_id' => $request->user()->employee->id,
        ]);

        return redirect()->route('facility.tickets.show', $facilityTicket)->with('success', 'Lampiran berhasil diunggah.');
    }

    public function destroy(FacilityTicket $facilityTicket, FacilityTicketAttachment $attachment): RedirectResponse
    {
        abort_unless($attachment->facility_ticket_id === $facilityTicket->id, 404);

        $attachment->delete();

        return redirect()->route('facility.tickets.show', $facilityTicket)->with('success', 'Lampiran berhasil dihapus.');
    }
}
