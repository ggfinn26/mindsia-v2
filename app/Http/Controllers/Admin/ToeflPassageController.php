<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Toefl\StoreToeflPassageRequest;
use App\Http\Requests\Toefl\UpdateToeflPassageRequest;
use App\Models\ToeflPassage;
use App\Models\ToeflTest;
use Illuminate\Http\RedirectResponse;

class ToeflPassageController extends Controller
{
    public function store(StoreToeflPassageRequest $request, ToeflTest $toeflTest): RedirectResponse
    {
        abort_unless($toeflTest->status === ToeflTest::STATUS_DRAFT, 403);

        $toeflTest->passages()->create($request->validated());

        return redirect()->route('toefl-tests.show', $toeflTest)->with('success', 'Passage ditambahkan.');
    }

    public function update(UpdateToeflPassageRequest $request, ToeflPassage $passage): RedirectResponse
    {
        abort_unless($passage->test->status === ToeflTest::STATUS_DRAFT, 403);

        $passage->update($request->validated());

        return redirect()->route('toefl-tests.show', $passage->toefl_test_id)->with('success', 'Passage diperbarui.');
    }

    public function destroy(ToeflPassage $passage): RedirectResponse
    {
        abort_unless($passage->test->status === ToeflTest::STATUS_DRAFT, 403);
        abort_if($passage->questions()->exists(), 422, 'Passage masih memiliki soal.');

        $passage->delete();

        return redirect()->back()->with('success', 'Passage dihapus.');
    }
}
