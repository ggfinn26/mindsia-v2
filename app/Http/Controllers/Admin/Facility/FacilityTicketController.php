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

    public function show(FacilityTicket $facilityTicket): View
    {
        $facilityTicket->load(['branch', 'createdBy', 'assignedTo', 'statusHistories.changedBy', 'attachments']);

        return view('facility.ticket.show', compact('facilityTicket'));
    }

    public function edit(FacilityTicket $facilityTicket): View
    {
        return view('facility.ticket.edit', compact('facilityTicket'));
    }

    public function update(UpdateFacilityTicketRequest $request, FacilityTicket $facilityTicket): RedirectResponse
    {
        $this->repository->update($facilityTicket, $request->validated());

        return redirect()->route('facility.tickets.show', $facilityTicket)->with('success', 'Tiket berhasil diperbarui.');
    }

    public function review(ReviewFacilityTicketRequest $request, FacilityTicket $facilityTicket): RedirectResponse
    {
        $data = $request->validated();
        $status = $data['action'] === 'approve' ? 'approved' : 'rejected';

        $this->repository->changeStatus($facilityTicket, $status, $request->user()->employee->id, $data['notes'] ?? null);

        return redirect()->route('facility.tickets.show', $facilityTicket)->with('success', 'Review berhasil disimpan.');
    }

    public function resolve(ResolveFacilityTicketRequest $request, FacilityTicket $facilityTicket): RedirectResponse
    {
        $data = $request->validated();
        $this->repository->changeStatus($facilityTicket, 'resolved', $request->user()->employee->id, $data['notes'] ?? null);

        if (isset($data['cost_amount'])) {
            $this->repository->update($facilityTicket, ['cost_amount' => $data['cost_amount']]);
        }

        return redirect()->route('facility.tickets.show', $facilityTicket)->with('success', 'Tiket berhasil diselesaikan.');
    }

    public function assign(AssignFacilityTicketRequest $request, FacilityTicket $facilityTicket): RedirectResponse
    {
        $this->repository->assign($facilityTicket, $request->validated()['assigned_to_employee_id']);

        return redirect()->route('facility.tickets.show', $facilityTicket)->with('success', 'Tiket berhasil di-assign.');
    }
}
