<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberSupportTicket;
use App\Models\MemberSupportTicketReply;
use App\Repositories\Member\MemberSupportTicketRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberMyTicketController extends Controller
{
    public function __construct(
        private readonly MemberSupportTicketRepository $repo,
    ) {}

    private function memberId(): int
    {
        return auth('member')->user()->memberData()->value('id');
    }

    public function index(): View
    {
        $memberId = $this->memberId();

        $tickets = MemberSupportTicket::where('member_id', $memberId)
            ->with(['branch', 'assignedEmployee'])
            ->orderByDesc('created_at')
            ->get();

        return view('member.tickets.index', compact('tickets'));
    }

    public function create(): View
    {
        $categories = ['complaint' => 'Pengaduan', 'suggestion' => 'Saran', 'question' => 'Pertanyaan'];

        return view('member.tickets.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10'],
            'category' => ['required', 'string', 'in:complaint,suggestion,question'],
            'priority' => ['required', 'string', 'in:low,medium,high'],
        ]);

        $member = auth('member')->user()->memberData;
        $validated['branch_id'] = $member->branch_id;
        $ticket = $this->repo->create($member, $validated);

        return redirect()->route('member.tickets.show', $ticket)
            ->with('success', 'Tiket berhasil dibuat. Nomor tiket: '.$ticket->ticket_number);
    }

    public function show(MemberSupportTicket $ticket): View
    {
        abort_unless($ticket->member_id === $this->memberId(), 403);

        $ticket->load(['branch', 'assignedEmployee', 'replies.employee', 'replies.member']);

        return view('member.tickets.show', compact('ticket'));
    }

    public function storeReply(Request $request, MemberSupportTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->member_id === $this->memberId(), 403);
        abort_if($ticket->status === 'closed', 403);

        $validated = $request->validate([
            'reply' => ['required', 'string', 'min:5'],
        ]);

        MemberSupportTicketReply::create([
            'member_support_ticket_id' => $ticket->id,
            'member_id' => $this->memberId(),
            'reply' => $validated['reply'],
        ]);

        return back()->with('success', 'Balasan berhasil dikirim.');
    }

    public function rate(Request $request, MemberSupportTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->member_id === $this->memberId(), 403);
        abort_unless(in_array($ticket->status, ['resolved', 'closed']), 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'rating_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $ticket->update($validated);

        return back()->with('success', 'Terima kasih atas penilaian Anda.');
    }
}
