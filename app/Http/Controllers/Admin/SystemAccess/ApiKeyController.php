<?php

namespace App\Http\Controllers\Admin\SystemAccess;

use App\Http\Controllers\Controller;
use App\Http\Requests\SystemAccess\StoreApiKeyRequest;
use App\Http\Requests\SystemAccess\UpdateApiKeyRequest;
use App\Models\ApiKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ApiKeyController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            (new Middleware('can:system.api_key.view'))->only(['index']),
            (new Middleware('can:system.api_key.create'))->only(['create', 'store']),
            (new Middleware('can:system.api_key.update'))->only(['edit', 'update', 'toggleActive']),
            (new Middleware('can:system.api_key.delete'))->only(['destroy']),
        ];
    }

    public function index(): View
    {
        return view('system-access.api-keys.index', [
            'keys' => ApiKey::orderBy('type')->orderBy('label')->get(),
        ]);
    }

    public function create(): View
    {
        return view('system-access.api-keys.create');
    }

    public function store(StoreApiKeyRequest $request): RedirectResponse
    {
        ApiKey::create($request->validated());

        return redirect()->route('system.api-keys.index')->with('success', 'API key berhasil ditambahkan.');
    }

    public function edit(ApiKey $apiKey): View
    {
        return view('system-access.api-keys.edit', compact('apiKey'));
    }

    public function update(UpdateApiKeyRequest $request, ApiKey $apiKey): RedirectResponse
    {
        $apiKey->update($request->validated());

        return redirect()->route('system.api-keys.index')->with('success', 'API key berhasil diperbarui.');
    }

    public function destroy(ApiKey $apiKey): RedirectResponse
    {
        $apiKey->delete();

        return redirect()->route('system.api-keys.index')->with('success', 'API key berhasil dihapus.');
    }

    public function toggleActive(ApiKey $apiKey): RedirectResponse
    {
        $apiKey->update(['is_active' => ! $apiKey->is_active]);

        return redirect()->route('system.api-keys.index')->with('success', 'Status key diubah.');
    }
}
