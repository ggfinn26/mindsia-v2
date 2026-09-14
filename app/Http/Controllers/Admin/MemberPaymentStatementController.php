<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberRegistration;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberPaymentStatementController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('marketing.member_payment_statement.view'), 403);

        $branchId = $request->input('branch_id');
        $sort = $request->input('sort', 'member_name');
        $direction = $request->input('direction', 'asc');

        $allowedSorts = ['member_name', 'institution', 'payment_status'];
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'member_name';
        }

        $query = MemberRegistration::with([
            'memberData.branch',
            'program',
            'payments' => fn ($q) => $q->orderBy('installment_number'),
        ])
            ->when($branchId, fn ($q) => $q->whereHas(
                'memberData',
                fn ($q2) => $q2->where('branch_id', $branchId)
            ));

        $registrations = $query->paginate(25)->withQueryString();

        return view('marketing.member-payment-statement.index', compact('registrations', 'sort', 'direction'));
    }
}
