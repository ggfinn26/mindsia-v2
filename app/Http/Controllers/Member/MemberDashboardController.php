<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberClass;
use App\Models\Program;
use App\Models\Province;
use Illuminate\View\View;

class MemberDashboardController extends Controller
{
    public function index(): View
    {
        $member = auth('member')->user();
        $memberDataId = $member->members_data_id;

        $activeClasses = MemberClass::with(['classRoom.program'])
            ->whereHas('memberRegistration', function ($q) use ($memberDataId) {
                $q->where('members_data_id', $memberDataId);
            })
            ->where('status', 'active')
            ->get();

        $availablePrograms = Program::where('is_active', true)->take(3)->get();
        $provinces = Province::orderBy('name')->get(['id', 'name']);

        return view('member.dashboard', [
            'member' => $member,
            'memberData' => $member->memberData,
            'activeClasses' => $activeClasses,
            'availablePrograms' => $availablePrograms,
            'provinces' => $provinces,
        ]);
    }
}
