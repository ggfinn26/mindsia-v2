<?php

namespace App\Services\Survey;

use App\Models\Employee;
use App\Models\MemberData;
use App\Models\Survey;
use App\Services\TelegramLogService;
use Illuminate\Support\Facades\DB;

class SurveyAssignmentService
{
    public function __construct(
        private readonly TelegramLogService $telegramLogService,
    ) {}

    public function assignToMembers(Survey $survey, ?array $memberIds): void
    {
        $ids = $memberIds ?? MemberData::pluck('id')->all();

        DB::transaction(function () use ($survey, $ids) {
            foreach ($ids as $memberId) {
                $survey->memberSurveys()->firstOrCreate(['member_id' => $memberId]);
            }
        });

        $this->telegramLogService->log('INFO', 'survey', 'assign_members', "Survey {$survey->id} di-assign ke ".count($ids).' member.');
    }

    public function assignToEmployees(Survey $survey, ?array $employeeIds): void
    {
        $ids = $employeeIds ?? Employee::pluck('id')->all();

        DB::transaction(function () use ($survey, $ids) {
            foreach ($ids as $employeeId) {
                $survey->employeeSurveys()->firstOrCreate(['employee_id' => $employeeId]);
            }
        });

        $this->telegramLogService->log('INFO', 'survey', 'assign_employees', "Survey {$survey->id} di-assign ke ".count($ids).' employee.');
    }
}
