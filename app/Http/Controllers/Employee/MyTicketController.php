<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\FacilityTicket;
use Illuminate\View\View;

class MyTicketController extends Controller
{
    public function index(): View
    {
        $employee = auth()->user()->employee;

        abort_if(! $employee, 403);

        $tickets = FacilityTicket::with('branch')
            ->where('created_by_employee_id', $employee->id)
            ->latest()
            ->paginate(20);

        return view('employee.tickets.index', compact('tickets'));
    }
}
