<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\ProgramPaymentPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProgramSeeder extends Seeder
{
    use WithoutModelEvents;

    private function plans(int $price): array
    {
        $half = intdiv($price, 2);
        $thirdRemainder = $price - $half; // sisa setelah DP 50%

        return [
            [
                'label' => 'Lunas',
                'type' => 'cash',
                'installment_count' => null,
                'dp_amount' => $price,
                'per_installment_amount' => null,
                'is_active' => true,
            ],
            [
                'label' => 'Cicilan 2x',
                'type' => 'installment',
                'installment_count' => 2,
                'dp_amount' => $half,
                'per_installment_amount' => $price - $half,
                'is_active' => true,
            ],
            [
                'label' => 'Cicilan 3x',
                'type' => 'installment',
                'installment_count' => 3,
                'dp_amount' => $half,
                'per_installment_amount' => intdiv($thirdRemainder, 2),
                'is_active' => true,
            ],
        ];
    }

    public function run(): void
    {
        $wordTemplateBase = dirname(base_path()).'/WORD-TEMPLATE';

        $programs = [
            [
                'program_name' => 'English Master',
                'program_code' => 'EM',
                'program_price' => 4_000_000,
                'is_active' => true,
                'docx_source' => $wordTemplateBase.'/FORM-ENGLISH-MASTER-PROGRAM.docx',
                'docx_dest' => 'program-templates/receipt-em.docx',
            ],
            [
                'program_name' => 'Junior English Pro',
                'program_code' => 'JEP',
                'program_price' => 6_000_000,
                'is_active' => true,
                'docx_source' => $wordTemplateBase.'/JUNIOR-ENGLISH-PRO.docx',
                'docx_dest' => 'program-templates/receipt-jep.docx',
            ],
            [
                'program_name' => 'English Fluency Program',
                'program_code' => 'EFP',
                'program_price' => 6_000_000,
                'is_active' => true,
                'docx_source' => $wordTemplateBase.'/KWITANSI-ENGLISH-FLUENCY-PROGRAM.docx',
                'docx_dest' => 'program-templates/receipt-efp.docx',
            ],
        ];

        foreach ($programs as $data) {
            $price = $data['program_price'];

            // Copy docx template ke storage jika belum ada
            $templatePath = null;
            if (file_exists($data['docx_source'])) {
                Storage::put($data['docx_dest'], file_get_contents($data['docx_source']));
                $templatePath = $data['docx_dest'];
            }

            $programData = [
                'program_name' => $data['program_name'],
                'program_price' => $price,
                'is_active' => $data['is_active'],
            ];

            if ($templatePath) {
                $programData['receipt_template_path'] = $templatePath;
            }

            $program = Program::firstOrCreate(
                ['program_code' => $data['program_code']],
                $programData,
            );

            if ($templatePath) {
                $program->update(['receipt_template_path' => $templatePath]);
            }

            foreach ($this->plans($price) as $plan) {
                ProgramPaymentPlan::firstOrCreate(
                    ['program_id' => $program->id, 'label' => $plan['label']],
                    $plan,
                );
            }
        }
    }
}
