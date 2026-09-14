<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ToeflSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestToeflLeadController extends Controller
{
    public function index(Request $request): View
    {
        $sessions = ToeflSession::whereNull('members_data_id')
            ->where('status', ToeflSession::STATUS_COMPLETED)
            ->with('test')
            ->when($request->input('city'), fn ($q, $v) => $q->where('guest_city', $v))
            ->when($request->input('toefl_test_id'), fn ($q, $v) => $q->where('toefl_test_id', $v))
            ->latest('completed_at')
            ->paginate(30);

        $cities = ToeflSession::whereNull('members_data_id')
            ->whereNotNull('guest_city')
            ->distinct()
            ->orderBy('guest_city')
            ->pluck('guest_city');

        return view('toefl.guest-lead.index', compact('sessions', 'cities'));
    }
}
