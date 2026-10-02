<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRoom\StoreMemberClassRequest;
use App\Http\Requests\ClassRoom\TransferMemberClassRequest;
use App\Models\ClassRoom;
use App\Models\MemberClass;
use App\Services\ClassRoomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MemberClassController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:class.manage')];
    }

    public function __construct(private ClassRoomService $service) {}

    public function store(StoreMemberClassRequest $request, ClassRoom $classroom): RedirectResponse
    {
        $this->service->enrollMember($classroom, $request->member_registration_id, $request->start_date);

        return back()->with('success', 'Member berhasil didaftarkan');
    }

    public function destroy(MemberClass $memberClass): RedirectResponse
    {
        $classId = $memberClass->class_id;
        $this->service->removeMember($memberClass);

        return redirect()->route('classrooms.show', $classId)->with('success', 'Member berhasil dihapus');
    }

    public function deactivate(MemberClass $memberClass): RedirectResponse
    {
        if ($memberClass->status !== 'active') {
            return back()->withErrors('Hanya member aktif yang bisa dinonaktifkan');
        }

        $this->service->deactivateMember($memberClass);

        return redirect()->route('classrooms.show', $memberClass->class_id)->with('success', 'Member berhasil dinonaktifkan');
    }

    public function transfer(TransferMemberClassRequest $request, MemberClass $memberClass): RedirectResponse
    {
        $newClass = ClassRoom::findOrFail($request->new_class_id);
        $this->service->transferMember($memberClass, $newClass);

        return redirect()->route('classrooms.show', $newClass)->with('success', 'Member berhasil dipindahkan');
    }
}
