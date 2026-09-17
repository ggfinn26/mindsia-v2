<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ToeflSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ToeflMemberSessionController extends Controller
{
    public function index(Request $request): View
    {
        $sessions = ToeflSession::whereNotNull('members_data_id')
            ->with(['member', 'test'])
            ->when($request->input('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($request->input('toefl_test_id'), fn ($q, $v) => $q->where('toefl_test_id', $v))
            ->latest('created_at')
            ->paginate(30);

        return view('toefl.member-session.index', compact('sessions'));
    }
}
