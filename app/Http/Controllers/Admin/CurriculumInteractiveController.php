<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Curriculum\StoreInteractiveBlockRequest;
use App\Models\CurriculumInteractiveBlock;
use App\Models\CurriculumInteractiveQuestion;
use App\Models\CurriculumItem;
use App\Models\CurriculumMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CurriculumInteractiveController extends Controller
{
    public function show(CurriculumItem $item): View
    {
        abort_unless(auth()->user()->can('curriculum.interactive.manage'), 403);
        abort_unless($item->material_type === 'interactive', 404);

        $item->load(['interactiveBlocks.questions', 'interactiveBlocks.audio', 'interactiveBlocks.image']);

        return view('curriculum.interactive.admin-edit', compact('item'));
    }

    public function store(StoreInteractiveBlockRequest $request, CurriculumItem $item): RedirectResponse
    {
        abort_unless($item->material_type === 'interactive', 422);

        $employeeId = auth()->user()->employee->id;

        DB::transaction(function () use ($request, $item, $employeeId) {
            $item->interactiveBlocks()->delete();

            foreach ($request->validated('blocks') as $bi => $blockData) {
                $audioId = $this->uploadMedia($request, "blocks.$bi.audio_file", 'audio', $employeeId)
                    ?? ($blockData['existing_audio_id'] ?? null);
                $imageId = $this->uploadMedia($request, "blocks.$bi.image_file", 'image', $employeeId)
                    ?? ($blockData['existing_image_id'] ?? null);

                $block = CurriculumInteractiveBlock::create([
                    'curriculum_item_id' => $item->id,
                    'sort_order' => $blockData['sort_order'],
                    'instruction_text' => $blockData['instruction_text'] ?? null,
                    'audio_media_id' => $audioId,
                    'image_media_id' => $imageId,
                ]);

                foreach ($blockData['questions'] ?? [] as $qData) {
                    CurriculumInteractiveQuestion::create([
                        'block_id' => $block->id,
                        'sort_order' => $qData['sort_order'],
                        'question_text' => $qData['question_text'],
                        'option_a' => $qData['option_a'],
                        'option_b' => $qData['option_b'],
                        'option_c' => $qData['option_c'],
                        'option_d' => $qData['option_d'],
                        'correct_option' => $qData['correct_option'],
                        'explanation' => $qData['explanation'] ?? null,
                    ]);
                }
            }
        });

        return back()->with('success', 'Modul interaktif berhasil disimpan.');
    }

    private function uploadMedia(StoreInteractiveBlockRequest $request, string $key, string $type, int $employeeId): ?int
    {
        $file = $request->file($key);

        if (! $file instanceof UploadedFile) {
            return null;
        }

        $path = $file->store("curriculum/media/$type", 'public');

        $media = CurriculumMedia::create([
            'media_type' => $type,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'uploaded_by_employee_id' => $employeeId,
        ]);

        return $media->id;
    }
}
