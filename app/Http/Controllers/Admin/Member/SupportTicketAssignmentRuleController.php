<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Models\SupportTicketAssignmentRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SupportTicketAssignmentRuleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:member.support.manage'),
        ];
    }

    public function index(): View
    {
        return view('admin.member.ticket-assignment-rules.index', [
            'rules' => SupportTicketAssignmentRule::with(['branch', 'position'])->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'in:complaint,suggestion,question'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'position_id' => ['required', 'integer', 'exists:positions,id'],
        ]);

        SupportTicketAssignmentRule::updateOrCreate(
            ['category' => $validated['category'], 'branch_id' => $validated['branch_id']],
            ['position_id' => $validated['position_id']],
        );

        return back()->with('success', 'Pengaturan assignment tiket berhasil disimpan.');
    }

    public function destroy(SupportTicketAssignmentRule $supportTicketAssignmentRule): RedirectResponse
    {
        $supportTicketAssignmentRule->delete();

        return back()->with('success', 'Pengaturan assignment tiket berhasil dihapus.');
    }
}
