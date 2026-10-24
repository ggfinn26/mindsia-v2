<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\MemberPayment;
use App\Models\MemberRegistration;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberPaymentStatementController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Tab scope digerakkan permission (per posisi). Urutan tab - sempit ke luas.
        $scopes = [];
        if ($user->can('marketing.member_payment_statement.view')) {
            $scopes[] = 'self';
        }
        if ($user->can('marketing.member_payment_statement.view_area')) {
            $scopes[] = 'area';
        }
        if ($user->can('marketing.member_payment_statement.view_national')) {
            $scopes[] = 'national';
        }
        abort_if(empty($scopes), 403);

        $scope = $request->input('scope');
        if (! in_array($scope, $scopes, true)) {
            $scope = $scopes[count($scopes) - 1]; // default scope terluas yang dimiliki
        }

        $employee = $user->employee;
        abort_if($scope !== 'national' && ! $employee, 403, 'Akun ini tidak terhubung ke data karyawan.');

        $branchId = $request->input('branch_id');
        $sort = $request->input('sort', 'member_name');
        $direction = $request->input('direction', 'asc');

        $allowedSorts = [
            'member_name', 'institution', 'payment_status',
            'program', 'whatsapp', 'dp', 'cicilan', 'total_dibayar', 'sisa',
        ];
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'member_name';
        }

        $programId = $request->input('program_id');
        $paymentStatus = $request->input('payment_status');

        $query = MemberRegistration::query()
            ->select('members_registration.*')
            ->with([
                'memberData.branch',
                'memberData.institution',
                'program',
                'payments' => fn ($q) => $q->orderBy('installment_number'),
            ])
            ->when($scope === 'self', fn ($q) => $q->whereHas(
                'memberData',
                fn ($q2) => $q2->where('referred_by_employee_id', $employee->id)
            ))
            ->when($scope === 'area', fn ($q) => $q->whereHas(
                'memberData',
                fn ($q2) => $q2->whereIn('branch_id', Branch::where('areas_id', $employee->area_id)->pluck('id'))
            ))
            ->when($branchId, fn ($q) => $q->whereHas(
                'memberData',
                fn ($q2) => $q2->where('branch_id', $branchId)
            ))
            ->when($programId, fn ($q) => $q->where('program_id', $programId))
            ->when($paymentStatus, fn ($q) => $q->where('payment_status', $paymentStatus));

        if ($sort === 'member_name') {
            $query->join('members_data', 'members_data.id', '=', 'members_registration.members_data_id')
                ->orderBy('members_data.full_name', $direction);
        } elseif ($sort === 'institution') {
            $query->join('members_data', 'members_data.id', '=', 'members_registration.members_data_id')
                ->leftJoin('institutions', 'institutions.id', '=', 'members_data.institution_id')
                ->orderByRaw('COALESCE(institutions.institution_name, members_data.institution_name_custom) '.$direction);
        } elseif ($sort === 'program') {
            $query->join('programs', 'programs.id', '=', 'members_registration.program_id')
                ->orderBy('programs.program_name', $direction);
        } elseif ($sort === 'whatsapp') {
            $query->join('members_data', 'members_data.id', '=', 'members_registration.members_data_id')
                ->orderBy('members_data.whatsapp_number', $direction);
        } elseif ($sort === 'payment_status') {
            $query->orderBy('members_registration.payment_status', $direction);
        } elseif ($sort === 'total_dibayar') {
            $query->withSum(['payments as total_paid_sum' => fn ($q) => $q->where('payment_status', 'paid')], 'amount')
                ->orderBy('total_paid_sum', $direction);
        } elseif ($sort === 'sisa') {
            $query->withSum(['payments as total_paid_sum' => fn ($q) => $q->where('payment_status', 'paid')], 'amount')
                ->orderByRaw('(members_registration.final_price - IFNULL(total_paid_sum, 0)) '.$direction);
        } elseif ($sort === 'cicilan') {
            $query->withCount(['payments as paid_installments_count' => fn ($q) => $q->where('payment_status', 'paid')])
                ->orderBy('paid_installments_count', $direction);
        } elseif ($sort === 'dp') {
            $query->addSelect([
                'dp_amount' => MemberPayment::select('amount')
                    ->whereColumn('member_registration_id', 'members_registration.id')
                    ->where('installment_number', 1)
                    ->where('payment_status', 'paid')
                    ->limit(1),
            ])->orderBy('dp_amount', $direction);
        }

        $registrations = $query->paginate(25)->withQueryString();

        return view('marketing.member-payment-statement.index', compact('registrations', 'sort', 'direction', 'scopes', 'scope'));
    }
}
