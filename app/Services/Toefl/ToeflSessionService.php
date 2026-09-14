<?php

namespace App\Services\Toefl;

use App\Models\MemberData;
use App\Models\ToeflSession;
use App\Models\ToeflTest;
use App\Repositories\Toefl\ToeflSessionRepository;
use App\Repositories\Toefl\ToeflTestRepository;
use Illuminate\Database\Eloquent\Collection;

class ToeflSessionService
{
    public function __construct(
        private readonly ToeflSessionRepository $sessionRepo,
        private readonly ToeflTestRepository $testRepo,
        private readonly ToeflScoringService $scoringService,
    ) {}

    public function startMemberSession(ToeflTest $test, MemberData $member, ?string $password): ToeflSession
    {
        abort_unless($test->status === ToeflTest::STATUS_PUBLISHED, 422, 'Test tidak tersedia.');
        abort_unless($this->testRepo->verifyPassword($test, $password), 403, 'Password salah.');

        $session = $this->sessionRepo->createMemberSession($test, $member->id);
        $this->sessionRepo->seedAnswers($session);

        return $session;
    }

    public function startGuestSession(ToeflTest $test, array $guestData): ToeflSession
    {
        abort_unless($test->is_trial && $test->status === ToeflTest::STATUS_PUBLISHED, 422, 'Test tidak tersedia.');
        abort_unless($this->testRepo->verifyPassword($test, $guestData['password'] ?? null), 403, 'Password salah.');

        $session = $this->sessionRepo->createGuestSession(
            $test,
            collect($guestData)->only([
                'guest_name', 'guest_email', 'guest_whatsapp',
                'guest_instagram', 'guest_institution', 'guest_city',
            ])->toArray()
        );

        $this->sessionRepo->seedAnswers($session);

        return $session;
    }

    public function getSectionQuestions(ToeflSession $session, string $section): Collection
    {
        $this->sessionRepo->markSectionStarted($session, $section);

        return $session->test->questions()
            ->where('section', $section)
            ->where('is_active', true)
            ->with(['passage', 'image'])
            ->orderBy('display_order')
            ->get()
            ->makeHidden(['correct_option', 'explanation']);
    }

    public function getSectionAnswers(ToeflSession $session, string $section): Collection
    {
        return $session->answers()
            ->whereHas('question', fn ($q) => $q->where('section', $section))
            ->get()
            ->keyBy('toefl_question_id');
    }

    public function saveAnswer(ToeflSession $session, array $data): void
    {
        abort_unless($session->status === ToeflSession::STATUS_IN_PROGRESS, 422, 'Session tidak aktif.');

        $this->sessionRepo->saveAnswer($session, $data['question_id'], $data['selected_option']);
    }

    public function submitSection(ToeflSession $session, string $section): void
    {
        abort_unless($session->status === ToeflSession::STATUS_IN_PROGRESS, 422, 'Session tidak aktif.');

        $rawCorrect = $this->sessionRepo->gradeSection($session, $section);

        $scaledScore = match ($section) {
            'listening' => $this->scoringService->convertListening($rawCorrect),
            'structure' => $this->scoringService->convertStructure($rawCorrect),
            'reading' => $this->scoringService->convertReading($rawCorrect),
        };

        $this->sessionRepo->submitSection($session, $section, $scaledScore);

        if ($section === 'reading') {
            $session->refresh();
            $total = $this->scoringService->calculateTotal(
                $session->score_listening,
                $session->score_structure,
                $session->score_reading,
            );
            $this->sessionRepo->finalize($session, $total);
        }
    }

    public function assertSessionBelongsToMember(ToeflSession $session, int $memberDataId): void
    {
        abort_unless($session->members_data_id === $memberDataId, 403);
    }

    public function assertGuestSession(ToeflSession $session): void
    {
        abort_unless(
            $session->isGuest() && session('guest_toefl_session_id') === $session->id,
            403
        );
    }
}
