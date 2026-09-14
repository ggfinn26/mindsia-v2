<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberNpsResponseRequest;
use App\Models\MemberNpsResponse;
use App\Models\MemberRegistration;
use App\Models\MemberReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberNpsResponseController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('member.manage'), 403);

        $responses = MemberNpsResponse::with(['member', 'branch'])
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('admin.member.nps.index', compact('responses'));
    }

    public function store(StoreMemberNpsResponseRequest $request): RedirectResponse
    {
        $memberId = auth('member')->user()->memberData()->value('id');

        MemberNpsResponse::create(array_merge(
            $request->safe()->only(['branch_id', 'score', 'comment']),
            ['member_id' => $memberId],
        ));

        // Combined form — also insert MemberReview (Gap 158)
        $registration = MemberRegistration::where('members_data_id', $memberId)
            ->where('graduation_status', 'LULUS')
            ->latest()
            ->first();

        if ($registration) {
            MemberReview::updateOrCreate(
                ['member_registration_id' => $registration->id],
                ['rating' => $request->validated('rating'), 'review' => $request->validated('review')],
            );
        }

        return redirect()->back()->with('success', 'Terima kasih atas penilaian Anda.');
    }

    public function updateReview(Request $request, MemberReview $review): RedirectResponse
    {
        // Ownership: review harus milik member yang login
        $memberId = auth('member')->user()?->members_data_id;
        $reviewOwnerId = $review->registration?->members_data_id;
        abort_unless($memberId && $memberId === $reviewOwnerId, 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'min:10'],
        ]);

        $review->update($validated);

        return back()->with('success', 'Review berhasil diperbarui.');
    }

    public function approveReview(MemberReview $review): RedirectResponse
    {
        abort_unless(auth()->user()->can('member.manage'), 403);

        $review->update(['is_approved' => true, 'approved_at' => now()]);

        return back()->with('success', 'Review berhasil disetujui.');
    }

    public function rejectReview(MemberReview $review): RedirectResponse
    {
        abort_unless(auth()->user()->can('member.manage'), 403);

        $review->update(['is_approved' => false, 'approved_at' => null]);

        return back()->with('success', 'Review ditolak.');
    }
}
