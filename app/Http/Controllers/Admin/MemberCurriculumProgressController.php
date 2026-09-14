<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRoom\UpdateMemberCurriculumProgressRequest;
use App\Models\MemberCurriculumProgress;
use Illuminate\Http\RedirectResponse;

class MemberCurriculumProgressController extends Controller
{
    public function update(UpdateMemberCurriculumProgressRequest $request, MemberCurriculumProgress $progress): RedirectResponse
    {
        $data = $request->validated();

        if ($data['status'] === 'in_progress' && ! $progress->started_at) {
            $data['started_at'] = now();
        }

        if ($data['status'] === 'completed' && ! $progress->completed_at) {
            $data['completed_at'] = now();
        }

        $progress->update($data);

        return back()->with('success', 'Progress kurikulum berhasil diperbarui.');
    }
}
