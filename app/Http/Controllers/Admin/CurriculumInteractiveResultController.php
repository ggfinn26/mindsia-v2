<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\CurriculumInteractiveAttempt;
use App\Models\CurriculumItem;
use App\Models\MemberClass;
use Illuminate\View\View;

class CurriculumInteractiveResultController extends Controller
{
    public function index(ClassRoom $classroom, CurriculumItem $item): View
    {
        abort_unless(auth()->user()->can('curriculum.interactive.result.view'), 403);
        abort_unless($item->material_type === 'interactive', 404);

        $results = MemberClass::where('class_id', $classroom->id)
            ->with(['registration.memberData'])
            ->get()
            ->map(function (MemberClass $memberClass) use ($item) {
                $latest = CurriculumInteractiveAttempt::where('member_class_id', $memberClass->id)
                    ->where('curriculum_item_id', $item->id)
                    ->whereNotNull('completed_at')
                    ->latest('completed_at')
                    ->first();

                return [
                    'member_class' => $memberClass,
                    'latest_attempt' => $latest,
                    'attempt_count' => CurriculumInteractiveAttempt::where('member_class_id', $memberClass->id)
                        ->where('curriculum_item_id', $item->id)
                        ->whereNotNull('completed_at')
                        ->count(),
                ];
            });

        return view('curriculum.interactive.tutor-results', compact('classroom', 'item', 'results'));
    }

    public function show(ClassRoom $classroom, CurriculumItem $item, MemberClass $memberClass): View
    {
        abort_unless(auth()->user()->can('curriculum.interactive.result.view'), 403);
        abort_unless($memberClass->class_id === $classroom->id, 404);

        $attempts = CurriculumInteractiveAttempt::where('member_class_id', $memberClass->id)
            ->where('curriculum_item_id', $item->id)
            ->whereNotNull('completed_at')
            ->with(['answers.question'])
            ->latest('completed_at')
            ->get();

        $item->load(['interactiveBlocks.questions', 'interactiveBlocks.audio', 'interactiveBlocks.image']);

        return view('curriculum.interactive.tutor-member-detail', compact('classroom', 'item', 'memberClass', 'attempts'));
    }
}
