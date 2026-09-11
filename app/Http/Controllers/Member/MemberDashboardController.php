<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MemberDashboardController extends Controller
{
    public function index(): View
    {
        $member = auth('member')->user();

        return view('member.dashboard', [
            'member' => $member,
            'memberData' => $member->memberData,
        ]);
    }
}
