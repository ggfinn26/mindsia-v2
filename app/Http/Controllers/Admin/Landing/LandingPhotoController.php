<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landing\StoreActivityPhotoRequest;
use App\Http\Requests\Landing\UpdateActivityPhotoRequest;
use App\Models\ActivityPhoto;
use App\Models\PhotoCategory;
use App\Services\TelegramStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingPhotoController extends Controller
{
    public function __construct(private TelegramStorageService $telegram) {}

    public function index(Request $request): View
    {
        $section = $request->input('section', 'company');
        $photos = ActivityPhoto::with('category')
            ->where('section', $section)
            ->orderBy('sort_order')
            ->get();

        return view('admin.landing.photos.index', compact('photos', 'section'));
    }

    public function create(Request $request): View
    {
        $section = $request->input('section', 'company');
        $categories = PhotoCategory::ordered()->get();

        return view('admin.landing.photos.create', compact('section', 'categories'));
    }

    public function store(StoreActivityPhotoRequest $request): RedirectResponse
    {
        $file = $request->file('photo');
        $result = $this->telegram->uploadFile(
            $file->getRealPath(),
            $file->getClientOriginalName(),
            'landing_photo',
            null
        );

        ActivityPhoto::create([
            'section' => $request->section,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'caption' => $request->caption,
            'storage_path' => $result['file_id'],
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->input('sort_order', 0),
        ]);

        return redirect()->route('admin.landing.photos.index', ['section' => $request->section])
            ->with('success', 'Foto berhasil ditambahkan.');
    }

    public function edit(ActivityPhoto $photo): View
    {
        $categories = PhotoCategory::ordered()->get();

        return view('admin.landing.photos.edit', compact('photo', 'categories'));
    }

    public function update(UpdateActivityPhotoRequest $request, ActivityPhoto $photo): RedirectResponse
    {
        $data = $request->only(['category_id', 'title', 'caption', 'sort_order']);
        $data['is_active'] = $request->boolean('is_active', $photo->is_active);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $result = $this->telegram->uploadFile(
                $file->getRealPath(),
                $file->getClientOriginalName(),
                'landing_photo',
                $photo->id
            );
            $data['storage_path'] = $result['file_id'];
        }

        $photo->update($data);

        return redirect()->route('admin.landing.photos.index', ['section' => $photo->section])
            ->with('success', 'Foto berhasil diperbarui.');
    }

    public function destroy(ActivityPhoto $photo): RedirectResponse
    {
        $section = $photo->section;
        $photo->delete();

        return redirect()->route('admin.landing.photos.index', ['section' => $section])
            ->with('success', 'Foto berhasil dihapus.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate(['items' => ['required', 'array'], 'items.*.id' => ['required', 'integer'], 'items.*.sort_order' => ['required', 'integer']]);

        foreach ($request->input('items') as $item) {
            ActivityPhoto::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['ok' => true]);
    }
}
