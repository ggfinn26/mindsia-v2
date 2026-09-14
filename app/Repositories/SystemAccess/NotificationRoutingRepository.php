<?php

namespace App\Repositories\SystemAccess;

use App\Models\NotificationRouting;
use Illuminate\Database\Eloquent\Collection;

class NotificationRoutingRepository
{
    public function all(): Collection
    {
        return NotificationRouting::with('position')->orderBy('event_key')->get();
    }

    public function find(int $id): NotificationRouting
    {
        return NotificationRouting::with('position')->findOrFail($id);
    }

    public function create(array $data): NotificationRouting
    {
        return NotificationRouting::create($data);
    }

    public function update(NotificationRouting $routing, array $data): NotificationRouting
    {
        $routing->update($data);

        return $routing;
    }

    public function delete(NotificationRouting $routing): void
    {
        $routing->delete();
    }
}
