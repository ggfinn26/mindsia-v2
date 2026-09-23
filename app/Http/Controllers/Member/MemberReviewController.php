<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberRegistration;
use App\Models\MemberReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberReviewController extends Controller
{
    public function index(): View
    {
        $memberId = auth('member')->user()->members_data_id;

        $registrations = MemberRegistration::with(['program', 'review'])
            ->where('members_data_id', $memberId)
            ->whereIn('graduation_status', ['LULUS', 'TIDAK LULUS'])
            ->orderByDesc('created_at')
            ->get();

        return view('member.reviews.index', compact('registrations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $memberId = auth('member')->user()->members_data_id;

        $validated = $request->validate([
            'member_registration_id' => ['required', 'integer', 'exists:member_registrations,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'min:10'],
        ]);

        $registration = MemberRegistration::where('id', $validated['member_registration_id'])
            ->where('members_data_id', $memberId)
            ->firstOrFail();

        MemberReview::create([
            'member_registration_id' => $registration->id,
            'rating' => $validated['rating'],
            'review' => $validated['review'],
        ]);

        return back()->with('success', 'Review berhasil dikirim, menunggu persetujuan.');
    }

    public function update(Request $request, MemberReview $review): RedirectResponse
    {
        $memberId = auth('member')->user()->members_data_id;
        abort_unless($review->registration?->members_data_id === $memberId, 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'min:10'],
        ]);

        $review->update($validated);

        return back()->with('success', 'Review berhasil diperbarui.');
    }
}
