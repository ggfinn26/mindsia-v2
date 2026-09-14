<?php

namespace App\Repositories\SystemAccess;

use App\Models\Bot;
use Illuminate\Database\Eloquent\Collection;

class BotRepository
{
    public function all(): Collection
    {
        return Bot::with('webhooks')->orderBy('name')->get();
    }

    public function find(int $id): Bot
    {
        return Bot::with('webhooks')->findOrFail($id);
    }

    public function create(array $data): Bot
    {
        return Bot::create($data);
    }

    public function update(Bot $bot, array $data): Bot
    {
        $bot->update($data);

        return $bot;
    }

    public function toggleActive(Bot $bot): Bot
    {
        $bot->update(['is_active' => ! $bot->is_active]);

        return $bot;
    }
}
