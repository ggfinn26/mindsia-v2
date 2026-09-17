<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class MemberNotificationController extends Controller
{
    public function index(): View
    {
        $memberId = auth('member')->user()->members_data_id;

        MemberNotification::where('member_id', $memberId)
            ->where('status', 'unread')
            ->update(['status' => 'read']);

        $notifications = MemberNotification::where('member_id', $memberId)
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('notification.member.index', compact('notifications'));
    }

    public function unreadCount(): JsonResponse
    {
        $count = MemberNotification::where('member_id', auth('member')->user()->members_data_id)
            ->where('status', 'unread')
            ->count();

        return response()->json(['count' => $count]);
    }
}
