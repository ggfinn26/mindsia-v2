<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberClass;
use Illuminate\View\View;

class MemberCurriculumController extends Controller
{
    public function index(): View
    {
        $memberDataId = auth('member')->user()->memberData->id;

        $memberClasses = MemberClass::whereHas('registration', fn ($q) => $q->where('members_data_id', $memberDataId))
            ->with([
                'classRoom.program.curriculum.sessions' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'classRoom.program.curriculum.sessions.items' => fn ($q) => $q->where('is_active', true)->orderBy('sequence_number'),
            ])
            ->get()
            ->filter(fn ($mc) => $mc->classRoom?->program?->curriculum !== null);

        return view('member.curriculum.index', compact('memberClasses'));
    }
}
