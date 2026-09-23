<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\MemberNpsResponse;
use App\Models\MemberRegistration;
use Illuminate\View\View;

class MemberNpsPageController extends Controller
{
    public function index(): View
    {
        $member = auth('member')->user();
        $memberData = $member->memberData;
        $memberId = $memberData?->id;

        $responses = $memberId
            ? MemberNpsResponse::with(['branch', 'program'])
                ->where('member_id', $memberId)
                ->orderByDesc('created_at')
                ->get()
            : collect();

        $branches = Branch::orderBy('branch_name')->get(['id', 'branch_name']);

        $programs = $memberId
            ? MemberRegistration::with('program')
                ->where('members_data_id', $memberId)
                ->get()
                ->pluck('program')
                ->filter()
                ->unique('id')
                ->values()
            : collect();

        $memberBranchId = $memberData?->branch_id;

        return view('member.nps.index', compact('responses', 'branches', 'programs', 'memberBranchId'));
    }
}
