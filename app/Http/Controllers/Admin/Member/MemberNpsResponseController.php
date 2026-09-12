<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberNpsResponseRequest;
use App\Models\MemberNpsResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MemberNpsResponseController extends Controller
{
    public function index(): View
    {
        $responses = MemberNpsResponse::with(['member', 'branch'])
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('admin.member.nps.index', compact('responses'));
    }

    public function store(StoreMemberNpsResponseRequest $request): RedirectResponse
    {
        $memberId = auth('member')->user()->memberData()->value('id');

        MemberNpsResponse::create(array_merge(
            $request->validated(),
            ['member_id' => $memberId],
        ));

        return redirect()->back()->with('success', 'Terima kasih atas penilaian Anda.');
    }
}
