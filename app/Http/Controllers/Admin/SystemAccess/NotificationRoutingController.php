<?php

namespace App\Http\Controllers\Admin\SystemAccess;

use App\Http\Controllers\Controller;
use App\Http\Requests\SystemAccess\StoreNotificationRoutingRequest;
use App\Http\Requests\SystemAccess\UpdateNotificationRoutingRequest;
use App\Models\NotificationRouting;
use App\Models\Position;
use App\Repositories\PositionRepository;
use App\Repositories\SystemAccess\NotificationRoutingRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationRoutingController extends Controller
{
    public function __construct(
        private readonly NotificationRoutingRepository $repository,
        private readonly PositionRepository $positionRepository,
    ) {}

    public static function systemEvents(): array
    {
        return [
            'Presensi & Cuti' => [
                'leave_request_created' => [
                    'label' => 'Pengajuan Izin / Sakit / Cuti Baru',
                    'desc' => 'Dikirim saat karyawan mengajukan permohonan cuti, sakit, atau izin.',
                    'default_channel' => 'in-app',
                    'default_scope' => 'branch',
                ],
                'leave_request_approved' => [
                    'label' => 'Izin / Sakit / Cuti Disetujui',
                    'desc' => 'Dikirim saat permohonan cuti disetujui oleh atasan / HR.',
                    'default_channel' => 'in-app',
                    'default_scope' => 'branch',
                ],
                'leave_request_rejected' => [
                    'label' => 'Izin / Sakit / Cuti Ditolak',
                    'desc' => 'Dikirim saat permohonan cuti ditolak.',
                    'default_channel' => 'in-app',
                    'default_scope' => 'branch',
                ],
                'attendance_anomaly_detected' => [
                    'label' => 'Anomali Kehadiran Pegawai',
                    'desc' => 'Peringatan keterlambatan berulang atau tidak check-out.',
                    'default_channel' => 'email',
                    'default_scope' => 'branch',
                ],
            ],
            'Payroll & Keuangan' => [
                'payroll_period_finalized' => [
                    'label' => 'Periode Payroll Finalized',
                    'desc' => 'Notifikasi saat periode penggajian difinalisasi dan slip siap.',
                    'default_channel' => 'in-app',
                    'default_scope' => 'global',
                ],
                'reimbursement_submitted' => [
                    'label' => 'Pengajuan Reimbursement Baru',
                    'desc' => 'Dikirim saat ada klaim reimbursement baru yang perlu direview.',
                    'default_channel' => 'in-app',
                    'default_scope' => 'branch',
                ],
                'reimbursement_approved' => [
                    'label' => 'Reimbursement Disetujui',
                    'desc' => 'Notifikasi approval reimbursement untuk diteruskan ke finance.',
                    'default_channel' => 'in-app',
                    'default_scope' => 'branch',
                ],
                'reimbursement_paid' => [
                    'label' => 'Reimbursement Dibayarkan',
                    'desc' => 'Notifikasi bahwa dana reimbursement telah dicairkan.',
                    'default_channel' => 'email',
                    'default_scope' => 'branch',
                ],
                'branch_cost_locked' => [
                    'label' => 'Biaya Operasional Cabang Dikunci',
                    'desc' => 'Pemberitahuan penutupan / kunci periode biaya cabang.',
                    'default_channel' => 'in-app',
                    'default_scope' => 'branch',
                ],
            ],
            'SDM & Kontrak' => [
                'contract_expiring_soon' => [
                    'label' => 'Kontrak Karyawan Akan Berakhir',
                    'desc' => 'Peringatan 30 hari sebelum masa kontrak kerja habis.',
                    'default_channel' => 'email',
                    'default_scope' => 'branch',
                ],
                'resign_request_submitted' => [
                    'label' => 'Pengajuan Resign Karyawan',
                    'desc' => 'Notifikasi saat karyawan mengajukan pengunduran diri.',
                    'default_channel' => 'in-app',
                    'default_scope' => 'branch',
                ],
                'branch_transfer_requested' => [
                    'label' => 'Permohonan Pindah Cabang',
                    'desc' => 'Notifikasi permohonan mutasi / transfer cabang.',
                    'default_channel' => 'in-app',
                    'default_scope' => 'area',
                ],
                'job_application_received' => [
                    'label' => 'Lamaran Kerja Baru Masuk',
                    'desc' => 'Notifikasi saat pelamar mengirimkan lamaran untuk lowongan aktif.',
                    'default_channel' => 'in-app',
                    'default_scope' => 'global',
                ],
            ],
            'Member & Pembelajaran' => [
                'member_registered' => [
                    'label' => 'Registrasi Member Baru',
                    'desc' => 'Pemberitahuan saat member baru selesai mendaftar program.',
                    'default_channel' => 'in-app',
                    'default_scope' => 'branch',
                ],
                'member_payment_confirmed' => [
                    'label' => 'Pembayaran Member Dikonfirmasi',
                    'desc' => 'Notifikasi penerimaan pembayaran cicilan / lunas member.',
                    'default_channel' => 'in-app',
                    'default_scope' => 'branch',
                ],
                'member_ticket_opened' => [
                    'label' => 'Tiket Bantuan Member Baru',
                    'desc' => 'Pemberitahuan pertanyaan / keluhan baru dari member.',
                    'default_channel' => 'in-app',
                    'default_scope' => 'branch',
                ],
            ],
            'Operasional & Fasilitas' => [
                'facility_ticket_created' => [
                    'label' => 'Tiket Kerusakan Fasilitas',
                    'desc' => 'Laporan kendala fasilitas atau sarana prasarana cabang.',
                    'default_channel' => 'telegram',
                    'default_scope' => 'branch',
                ],
                'facility_ticket_resolved' => [
                    'label' => 'Tiket Fasilitas Terselesaikan',
                    'desc' => 'Pemberitahuan bahwa perbaikan sarana telah rampung.',
                    'default_channel' => 'in-app',
                    'default_scope' => 'branch',
                ],
                'survey_published' => [
                    'label' => 'Survey Baru Ditugaskan',
                    'desc' => 'Instruksi pengisian kuesioner atau evaluasi.',
                    'default_channel' => 'in-app',
                    'default_scope' => 'global',
                ],
                'strategic_alert_triggered' => [
                    'label' => 'Peringatan Krisis Strategis (Alert Merah)',
                    'desc' => 'Notifikasi darurat terkait anomali operasional / keuangan.',
                    'default_channel' => 'telegram',
                    'default_scope' => 'global',
                ],
            ],
        ];
    }

    public function index(): View
    {
        $positions = Position::with(['role', 'notificationRoutings'])
            ->orderBy('position_name')
            ->get();

        return view('system-access.notification-routing.index', [
            'routings' => $this->repository->all(),
            'positions' => $positions,
        ]);
    }

    public function editPosition(Position $position): View
    {
        $position->load(['role', 'notificationRoutings']);

        $assignedRoutings = $position->notificationRoutings->groupBy('event_key');
        $systemEvents = self::systemEvents();

        // Check if there are any existing routings with event_key outside predefined ones
        $knownKeys = collect($systemEvents)->flatMap(fn ($items) => array_keys($items))->all();
        $customRoutings = $position->notificationRoutings->filter(fn ($r) => ! in_array($r->event_key, $knownKeys));
        if ($customRoutings->isNotEmpty()) {
            $systemEvents['Event Kustom / Lainnya'] = [];
            foreach ($customRoutings->groupBy('event_key') as $k => $rows) {
                $systemEvents['Event Kustom / Lainnya'][$k] = [
                    'label' => ucwords(str_replace(['_', '.'], ' ', $k)),
                    'desc' => $rows->first()->description ?: 'Event kustom sistem.',
                    'default_channel' => $rows->first()->channel,
                    'default_scope' => $rows->first()->scope,
                ];
            }
        }

        return view('system-access.notification-routing.edit-position', [
            'position' => $position,
            'systemEvents' => $systemEvents,
            'assignedRoutings' => $assignedRoutings,
        ]);
    }

    public function updatePosition(Request $request, Position $position): RedirectResponse
    {
        $validated = $request->validate([
            'events' => ['nullable', 'array'],
            'events.*.enabled' => ['nullable', 'boolean'],
            'events.*.channels' => ['nullable', 'array'],
            'events.*.channels.*' => ['in:in-app,email,telegram'],
            'events.*.scope' => ['nullable', 'in:global,branch,area'],
        ]);

        NotificationRouting::where('position_id', $position->id)->delete();

        foreach ($validated['events'] ?? [] as $eventKey => $data) {
            if (! empty($data['enabled'])) {
                $channels = ! empty($data['channels']) ? $data['channels'] : ['in-app'];
                $scope = $data['scope'] ?? 'branch';

                foreach ($channels as $channel) {
                    NotificationRouting::create([
                        'event_key' => $eventKey,
                        'position_id' => $position->id,
                        'channel' => $channel,
                        'scope' => $scope,
                        'is_active' => true,
                        'description' => 'Routing untuk '.$position->position_name,
                    ]);
                }
            }
        }

        return redirect()
            ->route('system.notification-routings.index')
            ->with('success', 'Routing notifikasi untuk posisi "'.$position->position_name.'" berhasil diperbarui.');
    }

    public function create(): View
    {
        return view('system-access.notification-routing.create', [
            'positions' => $this->positionRepository->all(),
        ]);
    }

    public function store(StoreNotificationRoutingRequest $request): RedirectResponse
    {
        $this->repository->create($request->validated());

        return redirect()->route('system.notification-routings.index')->with('success', 'Routing notifikasi berhasil disimpan.');
    }

    public function edit(NotificationRouting $notificationRouting): View
    {
        return view('system-access.notification-routing.edit', [
            'routing' => $notificationRouting,
            'positions' => $this->positionRepository->all(),
        ]);
    }

    public function update(UpdateNotificationRoutingRequest $request, NotificationRouting $notificationRouting): RedirectResponse
    {
        $this->repository->update($notificationRouting, $request->validated());

        return redirect()->route('system.notification-routings.index')->with('success', 'Routing berhasil diperbarui.');
    }

    public function destroy(NotificationRouting $notificationRouting): RedirectResponse
    {
        $this->repository->delete($notificationRouting);

        return redirect()->route('system.notification-routings.index')->with('success', 'Routing berhasil dihapus.');
    }
}
