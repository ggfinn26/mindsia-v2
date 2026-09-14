<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landing\StoreLandingTestimonialRequest;
use App\Http\Requests\Landing\UpdateLandingTestimonialRequest;
use App\Models\LandingTestimonial;
use App\Models\MemberReview;
use App\Services\TelegramStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LandingTestimonialController extends Controller
{
    public function __construct(private TelegramStorageService $telegram) {}

    public function index(): View
    {
        $testimonials = LandingTestimonial::orderBy('sort_order')->get();

        return view('admin.landing.testimonials.index', compact('testimonials'));
    }

    public function create(): View
    {
        return view('admin.landing.testimonials.create');
    }

    public function store(StoreLandingTestimonialRequest $request): RedirectResponse
    {
        $data = $request->only(['type', 'name', 'quote', 'program', 'city', 'member_review_id', 'sort_order']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $result = $this->telegram->uploadFile(
                $file->getRealPath(),
                $file->getClientOriginalName(),
                'landing_testimonial',
                null
            );
            $data['telegram_file_id'] = $result['telegram_file_id'];
        }

        LandingTestimonial::create($data);

        return redirect()->route('admin.landing.testimonials.index')
            ->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function edit(LandingTestimonial $testimonial): View
    {
        return view('admin.landing.testimonials.edit', compact('testimonial'));
    }

    public function update(UpdateLandingTestimonialRequest $request, LandingTestimonial $testimonial): RedirectResponse
    {
        $data = $request->only(['name', 'quote', 'program', 'city', 'member_review_id', 'sort_order']);
        $data['is_active'] = $request->boolean('is_active', $testimonial->is_active);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $result = $this->telegram->uploadFile(
                $file->getRealPath(),
                $file->getClientOriginalName(),
                'landing_testimonial',
                $testimonial->id
            );
            $data['telegram_file_id'] = $result['telegram_file_id'];
        }

        $testimonial->update($data);

        return redirect()->route('admin.landing.testimonials.index')
            ->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function destroy(LandingTestimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return redirect()->route('admin.landing.testimonials.index')
            ->with('success', 'Testimoni berhasil dihapus.');
    }

    /** Pre-fill create form from an existing MemberReview */
    public function fromReview(MemberReview $memberReview): View
    {
        $memberReview->load('registration');
        $prefill = [
            'name' => $memberReview->registration?->receipt_member_name ?? '',
            'program' => $memberReview->registration?->receipt_program_name ?? '',
            'quote' => $memberReview->review ?? '',
            'member_review_id' => $memberReview->id,
        ];

        return view('admin.landing.testimonials.create', ['prefill' => $prefill]);
    }
}
