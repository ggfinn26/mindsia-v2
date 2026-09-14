<?php

namespace App\Observers\Member;

use App\Models\Employee;
use App\Models\MemberSupportTicket;
use App\Models\MemberSupportTicketStatusHistory;
use App\Models\SupportTicketAssignmentRule;

class MemberSupportTicketObserver
{
    public function creating(MemberSupportTicket $ticket): void
    {
        // 1. Find the mapping rule based on category & branch_id (or fallback global rule where branch_id is null)
        $rule = SupportTicketAssignmentRule::where('category', $ticket->category)
            ->where(function ($query) use ($ticket) {
                $query->where('branch_id', $ticket->branch_id)
                    ->orWhereNull('branch_id');
            })
            ->orderBy('branch_id', 'desc') // prioritize specific branch over global (null)
            ->first();

        if ($rule) {
            // 2. Find an active employee in that branch that currently holds the required position
            $employee = Employee::where('branch_id', $ticket->branch_id)
                ->where('is_active', true)
                ->whereHas('currentStatus', function ($q) use ($rule) {
                    $q->where('position_id', $rule->position_id);
                })->first();

            // If we don't find someone in that exact branch, maybe fallback?
            // For now, assign if found.
            if ($employee) {
                $ticket->assigned_employee_id = $employee->id;
            } else {
                // Optional Fallback: Find any employee with that position regardless of branch if it's a global rule
                if (! $rule->branch_id) {
                    $globalEmployee = Employee::where('is_active', true)
                        ->whereHas('currentStatus', function ($q) use ($rule) {
                            $q->where('position_id', $rule->position_id);
                        })->first();
                    if ($globalEmployee) {
                        $ticket->assigned_employee_id = $globalEmployee->id;
                    }
                }
            }
        }
    }

    public function updating(MemberSupportTicket $ticket): void
    {
        if (! $ticket->isDirty('status')) {
            return;
        }

        MemberSupportTicketStatusHistory::create([
            'member_support_ticket_id' => $ticket->id,
            'from_status' => $ticket->getOriginal('status'),
            'to_status' => $ticket->status,
            'changed_by_employee_id' => auth('web')->id(),
            'changed_by_member_id' => auth('member')->id(),
            'changed_at' => now(),
        ]);
    }
}
