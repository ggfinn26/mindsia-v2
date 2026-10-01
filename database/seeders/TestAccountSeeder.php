<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAccountSeeder extends Seeder
{
    private const PASSWORD = 'Password1@';

    public function run(): void
    {
        $accounts = [
            [
                'label' => 'Regular Tutor (branch Serang)',
                'email' => 'tutor@test.id',
                'name' => 'Test Tutor',
                'branch_id' => 1,
                'area_id' => 1,
                'region_id' => 1,
                'is_hq' => false,
                'position_id' => 16, // Regular Tutor
                'permissions' => [
                    'class.attendance.record',
                    'class.session.update',
                    'class.assessment.record',
                    'class.progress.update',
                    'class.test.score',
                    'class.test.review_essay',
                    'kpi.document.create',
                    'finance.reimbursement.view',
                    'finance.reimbursement.create',
                    'facility.ticket.create',
                    'facility.ticket.update',
                ],
            ],
            [
                'label' => 'HR Personalia (HQ)',
                'email' => 'hr@test.id',
                'name' => 'Test HR Personalia',
                'branch_id' => null,
                'area_id' => null,
                'region_id' => null,
                'is_hq' => true,
                'position_id' => 9, // Human Resources Personalia
                'permissions' => [
                    'employee.view',
                    'employee.create',
                    'employee.update',
                    'employee.termination_checklist.review',
                    'contract.manage',
                    'organization.branch_transfer.review',
                    'organization.branch.assign_pic',
                    'organization.institution.create',
                    'organization.institution.update',
                    'recruitment.offering_letter.export',
                    'recruitment.onboarding.update',
                    'attendance.document_signature.manage',
                    'attendance.adjustment.create',
                    'attendance.leave.review',
                    'kpi.template.create',
                    'kpi.template.update',
                    'kpi.evaluator_assignment.create',
                    'kpi.evaluator_assignment.update',
                    'letter.template.create',
                    'letter.template.update',
                    'letter.generate.create',
                    'letter.generate.update',
                    'letter.generate.publish',
                    'letter.upload.create',
                    'letter.in.create',
                    'letter.sop.create',
                    'finance.reimbursement.view',
                    'finance.reimbursement.create',
                    'finance.reimbursement.review',
                    'finance.budget_estimate.send',
                    'notification.log.view',
                    'notification.send_manual',
                    'strategic_alerts.view',
                ],
            ],
            [
                'label' => 'PIC (branch Cilegon)',
                'email' => 'pic@test.id',
                'name' => 'Test PIC',
                'branch_id' => 2,
                'area_id' => 1,
                'region_id' => 1,
                'is_hq' => false,
                'position_id' => 12, // Person In Charge
                'permissions' => [
                    'employee.view',
                    'class.manage',
                    'class.member.assign',
                    'class.attendance.record',
                    'class.assessment.record',
                    'class.test.create',
                    'class.test.update',
                    'class.test.score',
                    'class.certificate.update',
                    'class.graduate',
                    'class.test.review_essay',
                    'member.view',
                    'member.manage',
                    'member.payment.manage',
                    'member.support.manage',
                    'finance.budget_estimate.view',
                    'finance.budget_estimate.create',
                    'finance.monthly_cost.view',
                    'finance.reimbursement.view',
                    'finance.reimbursement.create',
                    'facility.ticket.create',
                    'facility.ticket.assign',
                    'facility.ticket.review',
                    'facility.ticket.resolve',
                    'facility.inventory.create',
                    'facility.inventory.update',
                    'letter.generate.create',
                    'letter.upload.create',
                    'letter.in.create',
                    'recruitment.job_permintaan.create',
                    'recruitment.job_permintaan.update',
                    'marketing.monthly_revenue_data.view_branch',
                    'marketing.member_payment_statement.view',
                    'strategic_alerts.view',
                ],
            ],
            [
                'label' => 'Manager Area (area 1)',
                'email' => 'ma@test.id',
                'name' => 'Test Manager Area',
                'branch_id' => null,
                'area_id' => 1,
                'region_id' => 1,
                'is_hq' => false,
                'position_id' => 11, // Manager Area
                'permissions' => [
                    'employee.view',
                    'employee.update',
                    'contract.manage',
                    'class.manage',
                    'class.member.assign',
                    'member.view',
                    'member.manage',
                    'member.payment.manage',
                    'finance.budget_estimate.view',
                    'finance.budget_estimate.create',
                    'finance.budget_estimate.send',
                    'finance.reimbursement.view',
                    'finance.reimbursement.create',
                    'finance.reimbursement.review',
                    'finance.monthly_cost.view',
                    'marketing.socialization.create',
                    'marketing.socialization.update',
                    'marketing.socialization.assign',
                    'marketing.prospective_member.create',
                    'marketing.prospective_member.update',
                    'marketing.prospective.view_area',
                    'marketing.target.set',
                    'marketing.kpi.view',
                    'marketing.monthly_revenue_data.view_branch',
                    'marketing.member_payment_statement.view',
                    'recruitment.job_permintaan.create',
                    'recruitment.job_permintaan.update',
                    'kpi.evaluator_assignment.create',
                    'kpi.evaluation.create',
                    'kpi.evaluation.update',
                    'kpi.evaluation.finalize',
                    'facility.inventory.create',
                    'facility.inventory.update',
                    'survey.result.view',
                    'strategic_alerts.view',
                ],
            ],
        ];

        foreach ($accounts as $data) {
            $this->command->info("Creating: {$data['label']} ({$data['email']})");

            $employee = Employee::updateOrCreate(
                ['email' => $data['email']],
                [
                    'employee_code' => 'TEST-'.strtoupper(explode('@', $data['email'])[0]),
                    'full_name' => $data['name'],
                    'gender' => 'L',
                    'birthdate' => '1995-01-01',
                    'whatsapp_number' => '6281'.rand(10000000, 99999999),
                    'branch_id' => $data['branch_id'],
                    'area_id' => $data['area_id'],
                    'region_id' => $data['region_id'],
                    'is_hq' => $data['is_hq'],
                    'is_active' => true,
                    'join_date' => now()->subYear(),
                ]
            );

            EmploymentStatus::updateOrCreate(
                ['employees_id' => $employee->id],
                [
                    'type_employment' => 'contract',
                    'position_id' => $data['position_id'],
                    'contract_start_date' => now()->subYear(),
                    'contract_end_date' => now()->addYear(),
                    'setup_incomplete' => false,
                ]
            );

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make(self::PASSWORD),
                    'email_verified_at' => now(),
                    'employee_id' => $employee->id,
                    'is_active' => true,
                    'must_change_password' => false,
                ]
            );

            $user->syncPermissions($data['permissions']);
        }

        $this->command->info('Done. Password semua akun: '.self::PASSWORD);
    }
}
