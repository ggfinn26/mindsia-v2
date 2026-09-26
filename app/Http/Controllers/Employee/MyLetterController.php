<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeWarningLetter;
use App\Models\EmploymentStatus;
use App\Models\OutLetterViaGenerate;
use App\Services\TelegramStorageService;
use Illuminate\View\View;

class MyLetterController extends Controller
{
    public function __construct(
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    public function index(): View
    {
        $employee = auth()->user()->employee;

        abort_if(! $employee, 403);

        $warningLetters = EmployeeWarningLetter::with('template')
            ->where('employee_id', $employee->id)
            ->orderByDesc('issued_at')
            ->get();

        $generatedLetters = OutLetterViaGenerate::with('template')
            ->where('recipient_employee_id', $employee->id)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->get();

        $contracts = EmploymentStatus::where('employees_id', $employee->id)
            ->whereNotNull('contract_file_path')
            ->orderByDesc('contract_start_date')
            ->get();

        return view('employee.letters.index', compact(
            'employee',
            'warningLetters',
            'generatedLetters',
            'contracts',
        ));
    }

    public function downloadWarningLetter(EmployeeWarningLetter $warningLetter): mixed
    {
        abort_if($warningLetter->employee_id !== auth()->user()->employee?->id, 403);
        abort_unless($warningLetter->telegram_file_id, 404);

        $content = $this->telegramStorage->downloadFile($warningLetter->telegram_file_id);
        $filename = 'SP'.$warningLetter->sp_level.'_'.($warningLetter->letter_number ?? $warningLetter->id).'.pdf';

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
