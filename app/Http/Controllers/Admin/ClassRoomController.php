<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRoom\StoreClassRoomRequest;
use App\Http\Requests\ClassRoom\UpdateClassRoomRequest;
use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\MemberCertificate;
use App\Models\MemberClass;
use App\Models\MemberRegistration;
use App\Models\Program;
use App\Repositories\ClassRoomRepository;
use App\Services\Notification\NotificationDispatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ClassRoomController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:class.manage', except: ['show'])];
    }

    public function __construct(
        private ClassRoomRepository $repository,
        private NotificationDispatchService $notifService,
    ) {}

    public function index(): View
    {
        $filters = request()->only(['branch_id', 'program_id', 'status']);

        return view('admin.classroom.index', [
            'classes' => $this->repository->all($filters),
            'branches' => Branch::where('is_active', true)->orderBy('branch_name')->get(['id', 'branch_name']),
            'programs' => Program::where('is_active', true)->orderBy('program_name')->get(['id', 'program_name']),
            'statuses' => ['planned' => 'Direncanakan', 'active' => 'Aktif', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'],
        ]);
    }

    public function create(): View
    {
        return view('admin.classroom.create', [
            'programs' => Program::where('is_active', true)->get(),
            'branches' => Branch::where('is_active', true)->get(['id', 'branch_name', 'areas_id']),
            'primaryDays' => ClassRoom::PRIMARY_DAYS,
            'tutors' => $this->repository->getAvailableTutors(),
        ]);
    }

    public function store(StoreClassRoomRequest $request): RedirectResponse
    {
        $class = $this->repository->create($request->validated());

        return redirect()->route('classrooms.show', $class)->with('success', 'Kelas berhasil dibuat');
    }

    public function show(ClassRoom $classroom): View
    {
        $user = auth()->user();
        $isManager = $user->hasPermissionTo('class.manage');
        $isAssignedTutor = $user->hasPermissionTo('class.attendance.record')
            && $classroom->tutor_id === $user->employee?->id;

        if (! $isManager && ! $isAssignedTutor) {
            abort(403);
        }

        $class = $this->repository->findWithDetails($classroom->id);

        $availableRegistrations = MemberRegistration::with(['memberData', 'program'])
            ->where('program_id', $class->program_id)
            ->whereHas('memberData', function ($q) use ($class) {
                $q->where('branch_id', $class->branch_id)
                    ->whereHas('account', fn ($q) => $q->where('is_active', true));
            })
            ->whereNotIn('id', $class->memberClasses->pluck('member_registration_id'))
            ->get();

        $memberOptions = $availableRegistrations->map(fn ($r) => [
            'id' => $r->id,
            'label' => ($r->memberData?->full_name ?? 'Member #'.$r->id).' — '.($r->program?->program_name ?? ''),
        ])->values()->toArray();

        $transferableClasses = ClassRoom::where('program_id', $class->program_id)
            ->where('id', '!=', $class->id)
            ->where('status', 'active')
            ->get(['id', 'class_name']);

        return view('admin.classroom.show', compact('class', 'availableRegistrations', 'memberOptions', 'transferableClasses'));
    }

    public function edit(ClassRoom $classroom): View
    {
        return view('admin.classroom.edit', [
            'class' => $classroom,
            'programs' => Program::where('is_active', true)->get(),
            'branches' => Branch::where('is_active', true)->get(),
            'primaryDays' => ClassRoom::PRIMARY_DAYS,
            'tutors' => $this->repository->getAvailableTutors(),
        ]);
    }

    public function update(UpdateClassRoomRequest $request, ClassRoom $classroom): RedirectResponse
    {
        $this->repository->update($classroom, $request->validated());

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy(ClassRoom $classroom): RedirectResponse
    {
        if (! $classroom->canBeDeleted()) {
            return back()->withErrors('Hanya kelas planned tanpa member yang boleh dihapus');
        }
        $this->repository->delete($classroom);

        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil dihapus');
    }

    public function activate(ClassRoom $classroom): RedirectResponse
    {
        if ($classroom->status !== 'planned') {
            return back()->withErrors('Hanya kelas berstatus planned yang bisa diaktifkan');
        }

        if (! $classroom->schedules()->exists()) {
            return back()->withErrors('Kelas harus memiliki jadwal sebelum diaktifkan');
        }

        $classroom->update(['status' => 'active']);

        return back()->with('success', 'Kelas berhasil diaktifkan');
    }

    public function cancel(ClassRoom $classroom): RedirectResponse
    {
        if (! in_array($classroom->status, ['planned', 'active'])) {
            return back()->withErrors('Hanya kelas planned atau active yang bisa dibatalkan');
        }

        $classroom->update(['status' => 'cancelled', 'end_date' => today()]);

        return back()->with('success', 'Kelas berhasil dibatalkan');
    }

    public function graduate(ClassRoom $classroom): RedirectResponse
    {
        $this->authorize('class.graduate');

        $registrationIds = MemberClass::where('class_id', $classroom->id)
            ->where('status', 'active')
            ->whereHas('memberRegistration', fn ($q) => $q->where('graduation_status', '!=', 'LULUS'))
            ->pluck('member_registration_id');

        if ($registrationIds->isEmpty()) {
            return back()->withErrors('Tidak ada member aktif di kelas ini.');
        }

        DB::transaction(function () use ($classroom, $registrationIds) {
            MemberRegistration::whereIn('id', $registrationIds)
                ->update(['graduation_status' => 'LULUS']);

            MemberClass::where('class_id', $classroom->id)
                ->where('status', 'active')
                ->update(['status' => 'completed', 'end_date' => today()]);

            $now = now();
            $certificates = $registrationIds->map(fn ($regId) => [
                'member_registration_id' => $regId,
                'certificate_available' => 'not_available',
                'certificate_hardcopy' => true,
                'certificate_taken' => 'not_taken',
                'graduated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray();

            MemberCertificate::insertOrIgnore($certificates);

            $classroom->update(['status' => 'completed', 'end_date' => today()]);
        });

        // Post-graduation: kirim notifikasi review ke member yang baru lulus
        MemberRegistration::with('memberData')
            ->whereIn('id', $registrationIds)
            ->each(function ($reg) use ($classroom) {
                try {
                    $this->notifService->send('member_graduation_review_request', $reg->memberData, [
                        'member_name' => $reg->memberData->full_name,
                        'class_name' => $classroom->class_name,
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('Graduation notification failed', ['registration_id' => $reg->id, 'error' => $e->getMessage()]);
                }
            });

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Semua member berhasil diluluskan.');
    }
}
