<?php

namespace App\Services;

use App\Models\Curriculum;
use App\Models\CurriculumItem;
use App\Models\CurriculumSession;
use Illuminate\Support\Facades\DB;

class CurriculumContentService
{
    public function createWithSessions(array $data): Curriculum
    {
        return DB::transaction(function () use ($data) {
            $curriculum = Curriculum::create([
                'program_id' => $data['program_id'],
                'curriculum_name' => $data['curriculum_name'],
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (isset($data['sessions']) && is_array($data['sessions'])) {
                foreach ($data['sessions'] as $sessionData) {
                    $this->createSession($curriculum, $sessionData);
                }
            }

            return $curriculum->load('sessions.items');
        });
    }

    public function createSession(Curriculum $curriculum, array $data): CurriculumSession
    {
        return DB::transaction(function () use ($curriculum, $data) {
            $session = $curriculum->sessions()->create([
                'session_number' => $data['session_number'],
                'session_title' => $data['session_title'],
                'description' => $data['description'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $itemData) {
                    $this->createItem($session, $itemData);
                }
            }

            return $session->fresh('items');
        });
    }

    public function createItem(CurriculumSession $session, array $data): CurriculumItem
    {
        return $session->items()->create([
            'item_name' => $data['item_name'],
            'sequence_number' => $data['sequence_number'],
            'material_type' => $data['material_type'],
            'material_value' => $data['material_value'],
            'notes' => $data['notes'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function updateSession(CurriculumSession $session, array $data): CurriculumSession
    {
        return DB::transaction(function () use ($session, $data) {
            $session->update([
                'session_number' => $data['session_number'] ?? $session->session_number,
                'session_title' => $data['session_title'] ?? $session->session_title,
                'description' => $data['description'] ?? $session->description,
                'sort_order' => $data['sort_order'] ?? $session->sort_order,
                'is_active' => $data['is_active'] ?? $session->is_active,
            ]);

            return $session->fresh('items');
        });
    }

    public function updateItem(CurriculumItem $item, array $data): CurriculumItem
    {
        return $item->update([
            'item_name' => $data['item_name'] ?? $item->item_name,
            'sequence_number' => $data['sequence_number'] ?? $item->sequence_number,
            'material_type' => $data['material_type'] ?? $item->material_type,
            'material_value' => $data['material_value'] ?? $item->material_value,
            'notes' => $data['notes'] ?? $item->notes,
            'is_active' => $data['is_active'] ?? $item->is_active,
        ]) ? $item->fresh() : $item;
    }
}
