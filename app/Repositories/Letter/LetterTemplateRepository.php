<?php

namespace App\Repositories\Letter;

use App\Models\LetterTemplate;
use Illuminate\Database\Eloquent\Collection;

class LetterTemplateRepository
{
    public function all(): Collection
    {
        return LetterTemplate::orderBy('letter_category')->orderBy('template_name')->get();
    }

    public function active(string $category): Collection
    {
        return LetterTemplate::where('letter_category', $category)
            ->where('is_active', true)
            ->orderBy('template_name')
            ->get();
    }

    public function find(int $id): LetterTemplate
    {
        return LetterTemplate::findOrFail($id);
    }

    public function create(array $data): LetterTemplate
    {
        return LetterTemplate::create($data);
    }

    public function update(LetterTemplate $template, array $data): LetterTemplate
    {
        $template->update($data);

        return $template;
    }

    public function toggleActive(LetterTemplate $template): LetterTemplate
    {
        $template->update(['is_active' => ! $template->is_active]);

        return $template;
    }
}
