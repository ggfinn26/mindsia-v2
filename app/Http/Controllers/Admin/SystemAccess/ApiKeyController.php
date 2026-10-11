<?php

namespace App\Http\Controllers\Admin\SystemAccess;

use App\Http\Controllers\Controller;
use App\Http\Requests\SystemAccess\StoreApiKeyRequest;
use App\Http\Requests\SystemAccess\UpdateApiKeyRequest;
use App\Models\ApiKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class ApiKeyController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            (new Middleware('can:system.api_key.view'))->only(['index', 'checkQuota', 'totalQuota']),
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

    public function checkQuota(ApiKey $apiKey): JsonResponse
    {
        if ($apiKey->type !== 'convertapi') {
            return response()->json(['error' => 'Quota check tidak tersedia untuk tipe ini.'], 422);
        }

        try {
            $response = Http::timeout(8)
                ->withHeader('Authorization', 'Bearer '.$apiKey->key_value)
                ->get('https://v2.convertapi.com/user');

            if (! $response->successful()) {
                return response()->json(['error' => 'ConvertAPI menolak key ini. Cek apakah key masih valid.'], 502);
            }

            $data = $response->json();
            $secondsLeft = $data['SecondsLeft'] ?? null;
            $conversionsTotal = isset($data['ConversionsTotal']) ? (int) $data['ConversionsTotal'] : null;
            $conversionsConsumed = isset($data['ConversionsConsumed']) ? (int) $data['ConversionsConsumed'] : null;
            $remainingQuota = $conversionsTotal !== null && $conversionsConsumed !== null
                ? max(0, $conversionsTotal - $conversionsConsumed)
                : ($secondsLeft !== null ? (int) $secondsLeft : 0);

            return response()->json([
                'remaining_quota' => $remainingQuota,
                'seconds_left' => $secondsLeft,
                'conversions_total' => $conversionsTotal,
                'conversions_consumed' => $conversionsConsumed,
                'reset_date' => $data['SubscriptionValidUntil'] ?? null,
                'plan' => $data['ActivePlan'] ?? null,
            ]);
        } catch (\Throwable) {
            return response()->json(['error' => 'Tidak dapat terhubung ke ConvertAPI.'], 502);
        }
    }

    public function totalQuota(): JsonResponse
    {
        $keys = ApiKey::where('type', 'convertapi')->where('is_active', true)->get();

        if ($keys->isEmpty()) {
            return response()->json([
                'total_remaining_quota' => 0,
                'total_seconds_left' => 0,
                'total_conversions_consumed' => 0,
                'active_keys_count' => 0,
                'success_count' => 0,
                'error_count' => 0,
                'keys' => [],
            ]);
        }

        $totalRemainingQuota = 0;
        $totalSecondsLeft = 0;
        $totalConversionsConsumed = 0;
        $successCount = 0;
        $errorCount = 0;
        $keyDetails = [];

        foreach ($keys as $apiKey) {
            try {
                $response = Http::timeout(6)
                    ->withHeader('Authorization', 'Bearer '.$apiKey->key_value)
                    ->get('https://v2.convertapi.com/user');

                if ($response->successful()) {
                    $data = $response->json();
                    $seconds = (int) ($data['SecondsLeft'] ?? 0);
                    $consumed = (int) ($data['ConversionsConsumed'] ?? 0);
                    $total = isset($data['ConversionsTotal']) ? (int) $data['ConversionsTotal'] : null;
                    $remaining = $total !== null ? max(0, $total - $consumed) : $seconds;

                    $totalRemainingQuota += $remaining;
                    $totalSecondsLeft += $seconds;
                    $totalConversionsConsumed += $consumed;
                    $successCount++;

                    $keyDetails[] = [
                        'id' => $apiKey->id,
                        'label' => $apiKey->label,
                        'remaining_quota' => $remaining,
                        'seconds_left' => $seconds,
                        'conversions_total' => $total,
                        'conversions_consumed' => $consumed,
                        'plan' => $data['ActivePlan'] ?? null,
                        'status' => 'ok',
                    ];
                } else {
                    $errorCount++;
                    $keyDetails[] = [
                        'id' => $apiKey->id,
                        'label' => $apiKey->label,
                        'status' => 'failed',
                        'error' => 'HTTP '.$response->status(),
                    ];
                }
            } catch (\Throwable) {
                $errorCount++;
                $keyDetails[] = [
                    'id' => $apiKey->id,
                    'label' => $apiKey->label,
                    'status' => 'failed',
                    'error' => 'Connection timeout/error',
                ];
            }
        }

        return response()->json([
            'total_remaining_quota' => $totalRemainingQuota,
            'total_seconds_left' => $totalSecondsLeft,
            'total_conversions_consumed' => $totalConversionsConsumed,
            'active_keys_count' => $keys->count(),
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'keys' => $keyDetails,
        ]);
    }
}
