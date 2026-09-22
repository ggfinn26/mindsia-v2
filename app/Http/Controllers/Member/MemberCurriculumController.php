<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\CurriculumItem;
use App\Models\MemberClass;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MemberCurriculumController extends Controller
{
    public function index(): View
    {
        $memberDataId = auth('member')->user()->memberData->id;

        $memberClasses = MemberClass::whereHas('memberRegistration', fn ($q) => $q->where('members_data_id', $memberDataId))
            ->with([
                'classRoom.program.curriculum.sessions' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'classRoom.program.curriculum.sessions.items' => fn ($q) => $q->where('is_active', true)->orderBy('sequence_number'),
            ])
            ->get()
            ->filter(fn ($mc) => $mc->classRoom?->program?->curriculum !== null);

        return view('member.curriculum.index', compact('memberClasses'));
    }

    public function view(ClassRoom $classroom, CurriculumItem $item): View
    {
        $this->authorizeMemberAccess($classroom, $item);

        $fileType = match (true) {
            in_array(strtolower(pathinfo($item->material_value, PATHINFO_EXTENSION)), ['pdf']) => 'pdf',
            in_array(strtolower(pathinfo($item->material_value, PATHINFO_EXTENSION)), ['mp4', 'webm', 'ogg', 'mov']) => 'video',
            in_array(strtolower(pathinfo($item->material_value, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']) => 'image',
            in_array(strtolower(pathinfo($item->material_value, PATHINFO_EXTENSION)), ['mp3', 'wav', 'aac', 'm4a']) => 'audio',
            default => 'unknown',
        };

        return view('member.curriculum.viewer', [
            'classroom' => $classroom,
            'item' => $item,
            'fileType' => $fileType,
            'memberName' => auth('member')->user()->memberData->full_name,
            'mediaUrl' => route('member.curriculum.media', [$classroom, $item]),
        ]);
    }

    public function serveMedia(ClassRoom $classroom, CurriculumItem $item): BinaryFileResponse
    {
        $this->authorizeMemberAccess($classroom, $item);

        abort_if(! Storage::disk('public')->exists($item->material_value), 404);

        return response()->file(Storage::disk('public')->path($item->material_value));
    }

    private function authorizeMemberAccess(ClassRoom $classroom, CurriculumItem $item): void
    {
        $memberDataId = auth('member')->user()->memberData->id;

        $hasClass = $classroom->memberClasses()
            ->whereHas('memberRegistration', fn ($q) => $q->where('members_data_id', $memberDataId))
            ->exists();

        abort_unless(
            $hasClass &&
            $item->material_type === 'file' &&
            $item->material_value &&
            $item->session->curriculum_id === optional($classroom->program->curriculum)->id,
            403
        );
    }
}
