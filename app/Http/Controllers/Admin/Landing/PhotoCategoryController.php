<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landing\StorePhotoCategoryRequest;
use App\Http\Requests\Landing\UpdatePhotoCategoryRequest;
use App\Models\PhotoCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PhotoCategoryController extends Controller
{
    public function index(): View
    {
        $categories = PhotoCategory::ordered()->withCount('photos')->get();

        return view('admin.landing.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.landing.categories.create');
    }

    public function store(StorePhotoCategoryRequest $request): RedirectResponse
    {
        PhotoCategory::create($request->validated());

        return redirect()->route('admin.landing.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(PhotoCategory $category): View
    {
        return view('admin.landing.categories.edit', compact('category'));
    }

    public function update(UpdatePhotoCategoryRequest $request, PhotoCategory $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.landing.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(PhotoCategory $category): RedirectResponse
    {
        if ($category->photos()->exists()) {
            return redirect()->route('admin.landing.categories.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih dipakai foto. Pindahkan foto ke kategori lain dulu.');
        }

        $category->delete();

        return redirect()->route('admin.landing.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
