<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Toefl\StoreToeflMediaRequest;
use App\Models\ToeflMedia;
use App\Models\ToeflPassage;
use App\Models\ToeflQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ToeflMediaController extends Controller
{
    public function index(Request $request): View
    {
        $media = ToeflMedia::when($request->input('type'), fn ($q, $v) => $q->where('media_type', $v))
            ->latest()
            ->paginate(30);

        return view('toefl.media.index', compact('media'));
    }

    public function store(StoreToeflMediaRequest $request): JsonResponse
    {
        $file = $request->file('file');
        // ponytail: file stored to S3; swap for Telegram file_id when storage layer ready
        $path = $file->store("toefl/media/{$request->input('media_type')}", 's3');

        $media = ToeflMedia::create([
            'media_type' => $request->input('media_type'),
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'uploaded_by_employee_id' => $request->user()->employee->id,
        ]);

        return response()->json(['id' => $media->id, 'file_path' => $media->file_path]);
    }

    public function destroy(ToeflMedia $toeflMedia): RedirectResponse
    {
        $usedInPassage = $toeflMedia->media_type === 'audio'
            && ToeflPassage::where('audio_media_id', $toeflMedia->id)->exists();
        $usedInQuestion = ToeflQuestion::where('image_media_id', $toeflMedia->id)->exists();

        abort_if($usedInPassage || $usedInQuestion, 422, 'Media masih digunakan oleh soal atau passage.');

        $toeflMedia->delete();

        return redirect()->route('toefl.media.index')->with('success', 'Media dihapus.');
    }
}
