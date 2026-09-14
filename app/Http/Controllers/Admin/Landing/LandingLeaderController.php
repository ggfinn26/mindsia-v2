<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landing\StoreLandingLeaderRequest;
use App\Http\Requests\Landing\UpdateLandingLeaderRequest;
use App\Models\LandingLeader;
use App\Services\TelegramStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LandingLeaderController extends Controller
{
    public function __construct(private TelegramStorageService $telegram) {}

    public function index(): View
    {
        $leaders = LandingLeader::orderBy('sort_order')->get();

        return view('admin.landing.leaders.index', compact('leaders'));
    }

    public function create(): View
    {
        return view('admin.landing.leaders.create');
    }

    public function store(StoreLandingLeaderRequest $request): RedirectResponse
    {
        $data = $request->only(['name', 'title', 'sort_order']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $result = $this->telegram->uploadFile(
                $file->getRealPath(),
                $file->getClientOriginalName(),
                'landing_leader',
                null
            );
            $data['telegram_file_id'] = $result['telegram_file_id'];
        }

        LandingLeader::create($data);

        return redirect()->route('admin.landing.leaders.index')
            ->with('success', 'Pemimpin berhasil ditambahkan.');
    }

    public function edit(LandingLeader $leader): View
    {
        return view('admin.landing.leaders.edit', compact('leader'));
    }

    public function update(UpdateLandingLeaderRequest $request, LandingLeader $leader): RedirectResponse
    {
        $data = $request->only(['name', 'title', 'sort_order']);
        $data['is_active'] = $request->boolean('is_active', $leader->is_active);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $result = $this->telegram->uploadFile(
                $file->getRealPath(),
                $file->getClientOriginalName(),
                'landing_leader',
                $leader->id
            );
            $data['telegram_file_id'] = $result['telegram_file_id'];
        }

        $leader->update($data);

        return redirect()->route('admin.landing.leaders.index')
            ->with('success', 'Data pemimpin berhasil diperbarui.');
    }

    public function destroy(LandingLeader $leader): RedirectResponse
    {
        $leader->delete();

        return redirect()->route('admin.landing.leaders.index')
            ->with('success', 'Pemimpin berhasil dihapus.');
    }
}
