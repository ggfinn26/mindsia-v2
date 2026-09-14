<?php

namespace App\Repositories\Notification;

use App\Models\NotificationTemplate;
use App\Models\NotificationVariable;
use Illuminate\Database\Eloquent\Collection;

class NotificationTemplateRepository
{
    public function all(): Collection
    {
        return NotificationTemplate::with('variables')->orderBy('template_key')->get();
    }

    public function find(int $id): NotificationTemplate
    {
        return NotificationTemplate::with('variables')->findOrFail($id);
    }

    public function findByKey(string $key): ?NotificationTemplate
    {
        return NotificationTemplate::where('template_key', $key)->first();
    }

    public function create(array $data): NotificationTemplate
    {
        return NotificationTemplate::create($data);
    }

    public function update(NotificationTemplate $template, array $data): NotificationTemplate
    {
        $template->update($data);

        return $template;
    }

    public function delete(NotificationTemplate $template): void
    {
        if ($template->logs()->exists()) {
            throw new \RuntimeException('Template tidak bisa dihapus karena sudah pernah digunakan.');
        }

        $template->delete();
    }

    public function addVariable(NotificationTemplate $template, array $data): NotificationVariable
    {
        return $template->variables()->create($data);
    }

    public function updateVariable(NotificationVariable $variable, array $data): NotificationVariable
    {
        $variable->update($data);

        return $variable;
    }

    public function deleteVariable(NotificationVariable $variable): void
    {
        $variable->delete();
    }
}
