<?php

namespace Tests\Feature\Survey;

use App\Models\EmployeeSurvey;
use App\Models\MemberAccount;
use App\Models\MemberData;
use App\Models\MemberSurvey;
use App\Models\MemberSurveyAnswer;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionChoice;
use App\Models\User;
use App\Services\TelegramLogService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Covers: survey-template-management (SV), survey-assignment (SA),
//         member-survey-submit (MSS), employee-survey-submit (ESS),
//         survey-result-access (SRA)
class SurveyTest extends TestCase
{
    private User $adminUser;

    private User $regularUser;

    private int $employeeId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(TelegramLogService::class, fn ($m) => $m->shouldReceive('log')->andReturnNull());

        $uid = uniqid();
        $this->employeeId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-SV-{$uid}",
            'full_name' => 'Survey Admin',
            'gender' => 'L',
            'birthdate' => '1985-01-01',
            'email' => "sv.admin.{$uid}@test.com",
            'whatsapp_number' => '628100001111',
            'is_hq' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->adminUser = User::factory()->create(['employee_id' => $this->employeeId]);
        $this->adminUser->givePermissionTo([
            'survey.form.view', 'survey.form.create', 'survey.form.update',
            'survey.form.delete', 'survey.assignment.create', 'survey.result.view',
        ]);

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('HRR');
    }

    // ── Survey Creation Helpers ──────────────────────────────────────────

    private function createSurvey(array $overrides = []): Survey
    {
        return Survey::create(array_merge([
            'survey_name' => 'Test Survey',
            'survey_description' => 'Deskripsi test',
            'deadline_at' => null,
        ], $overrides));
    }

    private function addSingleChoiceQuestion(Survey $survey): array
    {
        $question = SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => 'Pilih salah satu',
            'question_type' => 'single_choice',
        ]);
        $choiceA = SurveyQuestionChoice::create(['survey_question_id' => $question->id, 'choice_text' => 'A']);
        $choiceB = SurveyQuestionChoice::create(['survey_question_id' => $question->id, 'choice_text' => 'B']);

        return ['question' => $question, 'choices' => [$choiceA, $choiceB]];
    }

    private function addTextQuestion(Survey $survey): SurveyQuestion
    {
        return SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => 'Tulis pendapat Anda',
            'question_type' => 'text',
        ]);
    }

    private function createMemberWithAccount(): array
    {
        $uid = uniqid();
        $provinceId = DB::table('provinces')->insertGetId(['name' => "Prov-SV-{$uid}", 'created_at' => now(), 'updated_at' => now()]);
        $regionId = DB::table('regions')->insertGetId(['province_id' => $provinceId, 'name' => "Reg-SV-{$uid}", 'created_at' => now(), 'updated_at' => now()]);

        $institutionId = DB::table('institutions')->insertGetId([
            'jenjang_institution' => 'SMA',
            'institution_name' => "Inst-SV-{$uid}",
            'regions_id' => $regionId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $memberData = MemberData::create([
            'full_name' => 'Survey Member',
            'email' => "sv.member.{$uid}@test.com",
            'whatsapp_number' => '628100002222',
            'institution_id' => $institutionId,
            'activation_status' => 'active',
        ]);

        $account = MemberAccount::create([
            'members_data_id' => $memberData->id,
            'email' => $memberData->email,
            'password' => 'password',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        return ['data' => $memberData, 'account' => $account];
    }

    // ── survey-template-management ───────────────────────────────────────

    // SV-01: buat survey berhasil
    public function test_admin_can_create_survey(): void
    {
        $this->actingAs($this->adminUser)
            ->post('/surveys', [
                'survey_name' => 'Survey Kepuasan',
                'questions' => [
                    [
                        'question_text' => 'Bagaimana layanan kami?',
                        'question_type' => 'single_choice',
                        'choices' => [
                            ['choice_text' => 'Bagus'],
                            ['choice_text' => 'Kurang'],
                        ],
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('surveys', ['survey_name' => 'Survey Kepuasan']);
        $this->assertDatabaseHas('survey_questions', ['question_text' => 'Bagaimana layanan kami?']);
    }

    // SV-02: tambah pertanyaan multi-tipe berhasil
    public function test_survey_store_creates_questions_with_choices(): void
    {
        $this->actingAs($this->adminUser)
            ->post('/surveys', [
                'survey_name' => 'Multi-type Survey',
                'questions' => [
                    [
                        'question_text' => 'Single choice?',
                        'question_type' => 'single_choice',
                        'choices' => [['choice_text' => 'Ya'], ['choice_text' => 'Tidak']],
                    ],
                    [
                        'question_text' => 'Free text?',
                        'question_type' => 'text',
                    ],
                ],
            ])
            ->assertRedirect();

        $survey = Survey::where('survey_name', 'Multi-type Survey')->first();
        $this->assertNotNull($survey);
        $this->assertCount(2, $survey->questions);
        $this->assertCount(2, $survey->questions->first()->choices);
    }

    // SV-03: survey immutable setelah ada jawaban → 500 (GAP — controller tidak catch RuntimeException)
    public function test_survey_update_with_existing_answers_throws_500(): void
    {
        $survey = $this->createSurvey();
        $question = $this->addTextQuestion($survey);

        $member = $this->createMemberWithAccount();
        $memberSurvey = MemberSurvey::create(['survey_id' => $survey->id, 'member_id' => $member['data']->id]);
        MemberSurveyAnswer::create([
            'member_survey_id' => $memberSurvey->id,
            'survey_question_id' => $question->id,
        ]);

        // SurveyController::update() now catches RuntimeException → redirect back with error
        $this->actingAs($this->adminUser)
            ->put("/surveys/{$survey->id}", [
                'survey_name' => 'Updated',
                'questions' => [['question_text' => 'Q?', 'question_type' => 'text']],
            ])
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    // SV-04: tanpa survey.form.create → 403
    public function test_user_without_create_permission_cannot_create_survey(): void
    {
        $this->actingAs($this->regularUser)
            ->post('/surveys', [
                'survey_name' => 'Forbidden',
                'questions' => [['question_text' => 'Q?', 'question_type' => 'text']],
            ])
            ->assertStatus(403);
    }

    // SV-05: hapus survey berhasil (tidak ada assignment)
    public function test_admin_can_delete_survey_without_assignments(): void
    {
        $survey = $this->createSurvey();

        $this->actingAs($this->adminUser)
            ->delete("/surveys/{$survey->id}")
            ->assertRedirect(route('surveys.index'));

        $this->assertDatabaseMissing('surveys', ['id' => $survey->id]);
    }

    // ── survey-assignment ────────────────────────────────────────────────

    // SA-01: assign broadcast ke semua member (null = semua)
    public function test_assign_survey_to_all_members_when_no_ids(): void
    {
        $survey = $this->createSurvey();
        $member = $this->createMemberWithAccount();

        $this->actingAs($this->adminUser)
            ->post("/surveys/{$survey->id}/assign-members", [
                'member_ids' => null,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('member_surveys', [
            'survey_id' => $survey->id,
            'member_id' => $member['data']->id,
        ]);
    }

    // SA-02/SA-03: assign bulk/single ke member IDs tertentu
    public function test_assign_survey_to_specific_member_ids(): void
    {
        $survey = $this->createSurvey();
        $member1 = $this->createMemberWithAccount();
        $member2 = $this->createMemberWithAccount();

        $this->actingAs($this->adminUser)
            ->post("/surveys/{$survey->id}/assign-members", [
                'member_ids' => [$member1['data']->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('member_surveys', ['survey_id' => $survey->id, 'member_id' => $member1['data']->id]);
        $this->assertDatabaseMissing('member_surveys', ['survey_id' => $survey->id, 'member_id' => $member2['data']->id]);
    }

    // SA-04: tanpa survey.assignment.create → 403
    public function test_user_without_assign_permission_cannot_assign(): void
    {
        $survey = $this->createSurvey();

        $this->actingAs($this->regularUser)
            ->post("/surveys/{$survey->id}/assign-members", ['member_ids' => null])
            ->assertStatus(403);
    }

    // Assign employees berhasil
    public function test_assign_survey_to_specific_employees(): void
    {
        $survey = $this->createSurvey();

        $this->actingAs($this->adminUser)
            ->post("/surveys/{$survey->id}/assign-employees", [
                'employee_ids' => [$this->employeeId],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('employee_surveys', [
            'survey_id' => $survey->id,
            'employee_id' => $this->employeeId,
        ]);
    }

    // ── member-survey-submit ─────────────────────────────────────────────

    // MSS-01: member submit survey berhasil
    public function test_member_can_submit_survey(): void
    {
        $survey = $this->createSurvey();
        $data = $this->addSingleChoiceQuestion($survey);
        $question = $data['question'];
        $choice = $data['choices'][0];

        $member = $this->createMemberWithAccount();
        $memberSurvey = MemberSurvey::create(['survey_id' => $survey->id, 'member_id' => $member['data']->id]);

        $this->actingAs($member['account'], 'member')
            ->post("/member/surveys/{$memberSurvey->id}/submit", [
                'answers' => [
                    ['question_id' => $question->id, 'choice_ids' => [$choice->id]],
                ],
            ])
            ->assertRedirect(route('member.surveys.index'));

        $this->assertDatabaseHas('member_survey_answers', ['member_survey_id' => $memberSurvey->id]);
    }

    // MSS-02: submit setelah deadline → service throws RuntimeException → 500
    public function test_member_cannot_submit_expired_survey(): void
    {
        $survey = $this->createSurvey(['deadline_at' => now()->subDay()]);
        $data = $this->addSingleChoiceQuestion($survey);
        $question = $data['question'];
        $choice = $data['choices'][0];

        $member = $this->createMemberWithAccount();
        $memberSurvey = MemberSurvey::create(['survey_id' => $survey->id, 'member_id' => $member['data']->id]);

        // SurveyAnswerService throws RuntimeException, controller does not catch → 500
        $this->actingAs($member['account'], 'member')
            ->post("/member/surveys/{$memberSurvey->id}/submit", [
                'answers' => [
                    ['question_id' => $question->id, 'choice_ids' => [$choice->id]],
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('error');
        // ponytail: GAP fixed — MemberSurveyController::store() now catches RuntimeException
    }

    // MSS-03: submit ulang → 500 (isSubmitted → RuntimeException, controller tidak catch)
    public function test_member_cannot_resubmit_survey(): void
    {
        $survey = $this->createSurvey();
        $question = $this->addTextQuestion($survey);

        $member = $this->createMemberWithAccount();
        $memberSurvey = MemberSurvey::create(['survey_id' => $survey->id, 'member_id' => $member['data']->id]);

        // Pre-create answer to simulate already submitted
        MemberSurveyAnswer::create([
            'member_survey_id' => $memberSurvey->id,
            'survey_question_id' => $question->id,
            'answer_text' => 'Jawaban pertama',
        ]);

        $this->actingAs($member['account'], 'member')
            ->post("/member/surveys/{$memberSurvey->id}/submit", [
                'answers' => [
                    ['question_id' => $question->id, 'answer_text' => 'Jawaban kedua'],
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('error');
        // ponytail: GAP fixed — MemberSurveyController::store() now catches RuntimeException
    }

    // MSS-04: single_choice max 1 pilihan — lebih dari 1 → validation error
    public function test_single_choice_answer_rejects_multiple_choices(): void
    {
        $survey = $this->createSurvey();
        $data = $this->addSingleChoiceQuestion($survey);
        $question = $data['question'];
        $choices = $data['choices'];

        $member = $this->createMemberWithAccount();
        $memberSurvey = MemberSurvey::create(['survey_id' => $survey->id, 'member_id' => $member['data']->id]);

        $this->actingAs($member['account'], 'member')
            ->post("/member/surveys/{$memberSurvey->id}/submit", [
                'answers' => [
                    ['question_id' => $question->id, 'choice_ids' => [$choices[0]->id, $choices[1]->id]],
                ],
            ])
            ->assertSessionHasErrors('answers.0.choice_ids');
    }

    // MSS-05: multiple_choice boleh banyak pilihan
    public function test_multiple_choice_accepts_multiple_choices(): void
    {
        $survey = $this->createSurvey();
        $question = SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => 'Pilih yang sesuai',
            'question_type' => 'multiple_choice',
        ]);
        $choice1 = SurveyQuestionChoice::create(['survey_question_id' => $question->id, 'choice_text' => 'A']);
        $choice2 = SurveyQuestionChoice::create(['survey_question_id' => $question->id, 'choice_text' => 'B']);
        $choice3 = SurveyQuestionChoice::create(['survey_question_id' => $question->id, 'choice_text' => 'C']);

        $member = $this->createMemberWithAccount();
        $memberSurvey = MemberSurvey::create(['survey_id' => $survey->id, 'member_id' => $member['data']->id]);

        $this->actingAs($member['account'], 'member')
            ->post("/member/surveys/{$memberSurvey->id}/submit", [
                'answers' => [
                    ['question_id' => $question->id, 'choice_ids' => [$choice1->id, $choice2->id, $choice3->id]],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('member_survey_answers', ['member_survey_id' => $memberSurvey->id]);
    }

    // ── employee-survey-submit ───────────────────────────────────────────

    // ESS-01: employee submit survey berhasil
    public function test_employee_can_submit_survey(): void
    {
        $survey = $this->createSurvey();
        $question = $this->addTextQuestion($survey);

        $employeeSurvey = EmployeeSurvey::create([
            'survey_id' => $survey->id,
            'employee_id' => $this->employeeId,
        ]);

        $this->actingAs($this->adminUser)
            ->post("/my-surveys/{$employeeSurvey->id}/submit", [
                'answers' => [
                    ['question_id' => $question->id, 'answer_text' => 'Jawaban saya'],
                ],
            ])
            ->assertRedirect(route('employee.surveys.index'));

        $this->assertDatabaseHas('employee_survey_answers', ['employee_survey_id' => $employeeSurvey->id]);
    }

    // ESS-02: submit ulang → 500 (same GAP pattern as MSS-03)
    public function test_employee_cannot_resubmit_survey(): void
    {
        $survey = $this->createSurvey();
        $question = $this->addTextQuestion($survey);

        $employeeSurvey = EmployeeSurvey::create([
            'survey_id' => $survey->id,
            'employee_id' => $this->employeeId,
        ]);

        // Create first answer
        DB::table('employee_survey_answers')->insert([
            'employee_survey_id' => $employeeSurvey->id,
            'survey_question_id' => $question->id,
            'answer_text' => 'Jawaban pertama',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->adminUser)
            ->post("/my-surveys/{$employeeSurvey->id}/submit", [
                'answers' => [
                    ['question_id' => $question->id, 'answer_text' => 'Jawaban kedua'],
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('error');
        // ponytail: GAP fixed — EmployeeSurveyController::store() now catches RuntimeException
    }

    // ── survey-result-access ─────────────────────────────────────────────

    // SRA-01: lihat hasil survey berhasil
    public function test_admin_can_view_survey_results(): void
    {
        $survey = $this->createSurvey();

        $this->actingAs($this->adminUser)
            ->get("/surveys/{$survey->id}/results")
            ->assertOk();
    }

    // SRA-02: tanpa survey.result.view → 403
    public function test_user_without_result_permission_cannot_view_results(): void
    {
        $survey = $this->createSurvey();

        $this->actingAs($this->regularUser)
            ->get("/surveys/{$survey->id}/results")
            ->assertStatus(403);
    }
}
