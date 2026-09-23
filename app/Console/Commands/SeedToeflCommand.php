<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\ToeflDirectionAudio;
use App\Models\ToeflMedia;
use App\Models\ToeflPassage;
use App\Models\ToeflQuestion;
use App\Models\ToeflTest;
use App\Services\TelegramStorageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedToeflCommand extends Command
{
    protected $signature = 'toefl:seed
        {--audio-dir= : Absolute path to TOEFL audio directory}
        {--json= : Absolute path to toefl JSON file}
        {--employee-id= : Employee ID used as creator/uploader}';

    protected $description = 'Seed a TOEFL test from JSON + audio directory into the database';

    public function __construct(private readonly TelegramStorageService $telegram)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $audioDir = $this->option('audio-dir') ?? base_path('../../TOEFL-AUDIO');
        $jsonPath = $this->option('json') ?? base_path('../../MOCK/toefl-sample.json');
        $employeeId = (int) ($this->option('employee-id') ?? Employee::value('id'));

        if (! $employeeId) {
            $this->error('No employee found. Pass --employee-id=N');

            return self::FAILURE;
        }

        if (! file_exists($jsonPath)) {
            $this->error("JSON not found: $jsonPath");

            return self::FAILURE;
        }

        if (! is_dir($audioDir)) {
            $this->error("Audio directory not found: $audioDir");

            return self::FAILURE;
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        DB::transaction(function () use ($audioDir, $data, $employeeId) {
            $mediaMap = $this->uploadAudios($audioDir, $employeeId);

            $this->seedDirectionAudios($mediaMap, $employeeId);

            $test = ToeflTest::create([
                'test_name' => $data['test_name'],
                'status' => $data['status'],
                'password' => $data['password'] ?? null,
                'is_trial' => $data['is_trial'] ?? false,
                'listening_time_limit' => $data['listening_time_limit'] ?? 35,
                'structure_time_limit' => $data['structure_time_limit'] ?? 25,
                'reading_time_limit' => $data['reading_time_limit'] ?? 55,
                'created_by_employee_id' => $employeeId,
            ]);

            $this->info("Test created: [{$test->id}] {$test->test_name}");

            $this->seedListening($test, $data['sections']['listening'], $mediaMap);
            $this->seedStructure($test, $data['sections']['structure']);
            $this->seedReading($test, $data['sections']['reading']);
        });

        $this->info('Seeding complete.');

        return self::SUCCESS;
    }

    /** Upload all .mp3 files; returns filename → media_id (or storage_path for non-media files). */
    private function uploadAudios(string $audioDir, int $employeeId): array
    {
        $map = [];

        foreach (glob("$audioDir/*.mp3") as $filePath) {
            $filename = basename($filePath);
            $this->line("  uploading $filename");

            $result = $this->telegram->uploadFile($filePath, $filename, 'audio');
            $storagePath = $result['file_id'];

            if (str_contains($filename, 'INSTRUCTIONS') || str_contains($filename, 'ENDING')) {
                // direction/ending audio: store path, not a toefl_media record
                $map[$filename] = $storagePath;
            } else {
                $media = ToeflMedia::create([
                    'media_type' => 'audio',
                    'original_name' => $filename,
                    'file_path' => $storagePath,
                    'uploaded_by_employee_id' => $employeeId,
                ]);
                $map[$filename] = $media->id;
            }
        }

        $this->info(count($map).' audio files uploaded.');

        return $map;
    }

    private function seedDirectionAudios(array $mediaMap, int $employeeId): void
    {
        $files = [
            'A' => 'SECTION-A-INSTRUCTIONS.mp3',
            'B' => 'SECTION-B-INSTRUCTIONS.mp3',
        ];

        foreach ($files as $part => $filename) {
            if (! isset($mediaMap[$filename])) {
                $this->warn("Direction audio Part $part not found, skipping.");

                continue;
            }

            ToeflDirectionAudio::updateOrCreate(
                ['part' => $part],
                ['file_path' => $mediaMap[$filename], 'uploaded_by_employee_id' => $employeeId]
            );

            $this->line("  direction audio Part $part seeded.");
        }
    }

    private function seedListening(ToeflTest $test, array $data, array $mediaMap): void
    {
        $passageOrder = 1;

        // Part A: mini-passage per question (each has its own audio)
        foreach ($data['parts']['A']['questions'] as $q) {
            $audioFilename = sprintf('SECTION-A-NUMBER-%d.mp3', $q['display_order']);

            $passage = ToeflPassage::create([
                'toefl_test_id' => $test->id,
                'section' => 'listening',
                'title' => "Part A - Question {$q['display_order']}",
                'body_text' => null,
                'audio_media_id' => $mediaMap[$audioFilename] ?? null,
                'display_order' => $passageOrder++,
            ]);

            ToeflQuestion::create([
                'toefl_test_id' => $test->id,
                'passage_id' => $passage->id,
                'section' => 'listening',
                'question_type' => 'multiple_choice',
                'display_order' => $q['display_order'],
                'question_text' => $q['question_text'],
                'option_a' => $q['option_a'],
                'option_b' => $q['option_b'],
                'option_c' => $q['option_c'],
                'option_d' => $q['option_d'],
                'correct_option' => $q['correct_option'],
                'difficulty' => $q['difficulty'],
                'explanation' => $q['explanation'] ?? null,
            ]);
        }

        // Part B & C: match passage to audio file by first question number
        foreach (['B', 'C'] as $part) {
            foreach ($data['parts'][$part]['passages'] as $passageData) {
                $firstQ = collect($passageData['questions'])->min('display_order');
                $audioPrefix = "SECTION-{$part}-NUMBER-{$firstQ}-TO-";

                $audioFilename = collect(array_keys($mediaMap))
                    ->first(fn ($f) => str_starts_with($f, $audioPrefix));

                $passage = ToeflPassage::create([
                    'toefl_test_id' => $test->id,
                    'section' => 'listening',
                    'title' => $passageData['title'],
                    'body_text' => null,
                    'audio_media_id' => $audioFilename ? $mediaMap[$audioFilename] : null,
                    'display_order' => $passageOrder++,
                ]);

                foreach ($passageData['questions'] as $q) {
                    ToeflQuestion::create([
                        'toefl_test_id' => $test->id,
                        'passage_id' => $passage->id,
                        'section' => 'listening',
                        'question_type' => 'multiple_choice',
                        'display_order' => $q['display_order'],
                        'question_text' => $q['question_text'],
                        'option_a' => $q['option_a'],
                        'option_b' => $q['option_b'],
                        'option_c' => $q['option_c'],
                        'option_d' => $q['option_d'],
                        'correct_option' => $q['correct_option'],
                        'difficulty' => $q['difficulty'],
                        'explanation' => $q['explanation'] ?? null,
                    ]);
                }
            }
        }

        $this->info('Listening seeded.');
    }

    private function seedStructure(ToeflTest $test, array $data): void
    {
        foreach ($data['parts']['structure']['questions'] as $q) {
            ToeflQuestion::create([
                'toefl_test_id' => $test->id,
                'section' => 'structure',
                'question_type' => 'multiple_choice',
                'display_order' => $q['display_order'],
                'question_text' => $q['question_text'],
                'option_a' => $q['option_a'],
                'option_b' => $q['option_b'],
                'option_c' => $q['option_c'],
                'option_d' => $q['option_d'],
                'correct_option' => $q['correct_option'],
                'difficulty' => $q['difficulty'],
                'explanation' => $q['explanation'] ?? null,
            ]);
        }

        foreach ($data['parts']['written_expression']['questions'] as $q) {
            ToeflQuestion::create([
                'toefl_test_id' => $test->id,
                'section' => 'structure',
                'question_type' => 'written_expression',
                'display_order' => $q['display_order'],
                'question_text' => $q['question_text'],
                'option_a' => $q['option_a'],
                'option_b' => $q['option_b'],
                'option_c' => $q['option_c'],
                'option_d' => $q['option_d'],
                'correct_option' => $q['correct_option'],
                'difficulty' => $q['difficulty'],
                'explanation' => $q['explanation'] ?? null,
            ]);
        }

        $this->info('Structure seeded.');
    }

    private function seedReading(ToeflTest $test, array $data): void
    {
        $passageOrder = 1;

        foreach ($data['passages'] as $passageData) {
            $bodyText = is_array($passageData['body_text'])
                ? implode("\n\n", $passageData['body_text'])
                : $passageData['body_text'];

            $passage = ToeflPassage::create([
                'toefl_test_id' => $test->id,
                'section' => 'reading',
                'title' => $passageData['title'],
                'body_text' => $bodyText,
                'audio_media_id' => null,
                'display_order' => $passageOrder++,
            ]);

            foreach ($passageData['questions'] as $q) {
                ToeflQuestion::create([
                    'toefl_test_id' => $test->id,
                    'passage_id' => $passage->id,
                    'section' => 'reading',
                    'question_type' => 'multiple_choice',
                    'display_order' => $q['display_order'],
                    'question_text' => $q['question_text'],
                    'option_a' => $q['option_a'],
                    'option_b' => $q['option_b'],
                    'option_c' => $q['option_c'],
                    'option_d' => $q['option_d'],
                    'correct_option' => $q['correct_option'],
                    'difficulty' => $q['difficulty'],
                    'explanation' => $q['explanation'] ?? null,
                ]);
            }
        }

        $this->info('Reading seeded.');
    }
}
