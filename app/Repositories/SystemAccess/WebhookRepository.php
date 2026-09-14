<?php

namespace App\Repositories\SystemAccess;

use App\Models\Webhook;
use Illuminate\Database\Eloquent\Collection;

class WebhookRepository
{
    public function all(): Collection
    {
        return Webhook::with('bot')->orderBy('name')->get();
    }

    public function find(int $id): Webhook
    {
        return Webhook::with('bot')->findOrFail($id);
    }

    public function create(array $data): Webhook
    {
        return Webhook::create($data);
    }

    public function update(Webhook $webhook, array $data): Webhook
    {
        $webhook->update($data);

        return $webhook;
    }

    public function delete(Webhook $webhook): void
    {
        $webhook->delete();
    }

    public function recordPing(Webhook $webhook, bool $success): Webhook
    {
        $webhook->update([
            'last_ping_at' => now(),
            'last_status' => $success ? 'ok' : 'failed',
        ]);

        return $webhook;
    }
}
