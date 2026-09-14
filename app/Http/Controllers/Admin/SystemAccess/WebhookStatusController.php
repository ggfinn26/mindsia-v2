<?php

namespace App\Http\Controllers\Admin\SystemAccess;

use App\Http\Controllers\Controller;
use App\Http\Requests\SystemAccess\StoreWebhookRequest;
use App\Http\Requests\SystemAccess\UpdateWebhookRequest;
use App\Models\Webhook;
use App\Repositories\SystemAccess\WebhookRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\IpUtils;

class WebhookStatusController extends Controller
{
    private const BLOCKED_RANGES = [
        '127.0.0.0/8',
        '::1/128',
        '169.254.0.0/16',  // cloud metadata (AWS, GCP, Azure)
        'fe80::/10',
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',
        '0.0.0.0/8',
        '100.64.0.0/10',   // CGNAT
    ];

    public function __construct(
        private readonly WebhookRepository $repository,
    ) {}

    public function index(): View
    {
        return view('system-access.webhook.index', [
            'webhooks' => $this->repository->all(),
        ]);
    }

    public function create(): View
    {
        return view('system-access.webhook.create');
    }

    public function store(StoreWebhookRequest $request): RedirectResponse
    {
        $this->repository->create($request->validated());

        return redirect()->route('system.webhooks.index')->with('success', 'Webhook berhasil ditambahkan.');
    }

    public function edit(Webhook $webhook): View
    {
        return view('system-access.webhook.edit', compact('webhook'));
    }

    public function update(UpdateWebhookRequest $request, Webhook $webhook): RedirectResponse
    {
        $this->repository->update($webhook, $request->validated());

        return redirect()->route('system.webhooks.index')->with('success', 'Webhook berhasil diperbarui.');
    }

    public function destroy(Webhook $webhook): RedirectResponse
    {
        $this->repository->delete($webhook);

        return redirect()->route('system.webhooks.index')->with('success', 'Webhook berhasil dihapus.');
    }

    public function ping(Webhook $webhook): RedirectResponse
    {
        try {
            $safeIp = self::resolveSafeIp($webhook->endpoint_url);

            $parsed = parse_url($webhook->endpoint_url);
            $host = $parsed['host'];
            $port = $parsed['port'] ?? (($parsed['scheme'] === 'https') ? 443 : 80);

            // Pin resolved IP via CURLOPT_RESOLVE to prevent DNS rebinding TOCTOU
            $response = Http::timeout(5)
                ->withOptions([
                    'allow_redirects' => false,
                    'curl' => [CURLOPT_RESOLVE => ["{$host}:{$port}:{$safeIp}"]],
                ])
                ->get($webhook->endpoint_url);

            $success = $response->successful();
        } catch (\Throwable) {
            $success = false;
        }

        $this->repository->recordPing($webhook, $success);

        $msg = $success ? 'Webhook aktif.' : 'Webhook tidak merespons.';

        return redirect()->route('system.webhooks.index')->with($success ? 'success' : 'error', $msg);
    }

    /**
     * Validate URL scheme + resolve DNS + check all IPs against denylist.
     * Returns the first safe resolved IP for connection pinning.
     * Throws \RuntimeException if URL is not safe.
     */
    public static function resolveSafeIp(string $url): string
    {
        $parsed = parse_url($url);

        if (! in_array($parsed['scheme'] ?? '', ['http', 'https'], true)) {
            throw new \RuntimeException('Only http/https schemes allowed.');
        }

        $host = $parsed['host'] ?? '';
        if ($host === '') {
            throw new \RuntimeException('Missing host.');
        }

        $resolved = gethostbynamel($host);
        if ($resolved === false || $resolved === []) {
            throw new \RuntimeException('Cannot resolve host.');
        }

        foreach ($resolved as $ip) {
            if (IpUtils::checkIp($ip, self::BLOCKED_RANGES)) {
                throw new \RuntimeException("Resolved IP {$ip} is in a blocked range.");
            }
        }

        return $resolved[0];
    }

    /** Kept for backward compatibility with Form Requests. */
    public static function assertSafeUrl(string $url): void
    {
        self::resolveSafeIp($url);
    }
}
