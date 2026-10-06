<?php

namespace App\Http\Controllers\Admin\Facility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Facility\AssignFacilityTicketRequest;
use App\Http\Requests\Facility\ResolveFacilityTicketRequest;
use App\Http\Requests\Facility\ReviewFacilityTicketRequest;
use App\Http\Requests\Facility\StoreFacilityTicketRequest;
use App\Http\Requests\Facility\UpdateFacilityTicketRequest;
use App\Models\FacilityTicket;
use App\Repositories\Facility\FacilityTicketRepository;
use App\Services\Facility\FacilityTicketNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FacilityTicketController extends Controller
{
    public function __construct(
        private readonly FacilityTicketRepository $repository,
        private readonly FacilityTicketNumberService $ticketNumberService,
    ) {}

    public function index(): View
    {
        return view('facility.ticket.index');
    }

    public function create(): View
    {
        return view('facility.ticket.create');
    }

    public function store(StoreFacilityTicketRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['ticket_number'] = $this->ticketNumberService->generate($data['category'], $data['branch_id']);
        $data['created_by_employee_id'] = $request->user()->employee->id;

        $ticket = $this->repository->create($data);

        $this->repository->changeStatus($ticket, 'pending_review', $request->user()->employee->id, null);

        return redirect()->route('facility.tickets.show', $ticket)->with('success', 'Tiket berhasil dibuat.');
    }

    public function show(FacilityTicket $ticket): View
    {
        $ticket->load(['branch', 'createdBy', 'assignedTo', 'statusHistories.changedBy', 'attachments']);

        return view('facility.ticket.show', ['facilityTicket' => $ticket]);
    }

    public function edit(FacilityTicket $ticket): View
    {
        return view('facility.ticket.edit', ['facilityTicket' => $ticket]);
    }

    public function update(UpdateFacilityTicketRequest $request, FacilityTicket $ticket): RedirectResponse
    {
        $this->repository->update($ticket, $request->validated());

        return redirect()->route('facility.tickets.show', $ticket)->with('success', 'Tiket berhasil diperbarui.');
    }

    public function review(ReviewFacilityTicketRequest $request, FacilityTicket $ticket): RedirectResponse
    {
        $data = $request->validated();
        $status = $data['action'] === 'approve' ? 'approved' : 'rejected';

        $this->repository->changeStatus($ticket, $status, $request->user()->employee->id, $data['notes'] ?? null);

        return redirect()->route('facility.tickets.show', $ticket)->with('success', 'Review berhasil disimpan.');
    }

    public function resolve(ResolveFacilityTicketRequest $request, FacilityTicket $ticket): RedirectResponse
    {
        $data = $request->validated();
        $this->repository->changeStatus($ticket, 'resolved', $request->user()->employee->id, $data['notes'] ?? null);

        if (isset($data['cost_amount'])) {
            $this->repository->update($ticket, ['cost_amount' => $data['cost_amount']]);
        }

        return redirect()->route('facility.tickets.show', $ticket)->with('success', 'Tiket berhasil diselesaikan.');
    }

    public function assign(AssignFacilityTicketRequest $request, FacilityTicket $ticket): RedirectResponse
    {
        $this->repository->assign($ticket, $request->validated()['assigned_to_employee_id']);

        return redirect()->route('facility.tickets.show', $ticket)->with('success', 'Tiket berhasil di-assign.');
    }
}
