<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\TelegramStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class EmployeeProfileController extends Controller
{
    public function __construct(
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    public function show()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $photoUrl = null;

        if ($employee && $employee->image_path) {
            if (str_starts_with($employee->image_path, 'telegram:')) {
                $fileId = str_replace('telegram:', '', $employee->image_path);
                try {
                    $photoUrl = $this->telegramStorage->getFileUrl($fileId);
                } catch (\Exception $e) {
                    Log::error('Failed to get telegram photo: '.$e->getMessage());
                }
            } else {
                $photoUrl = Storage::disk('public')->url($employee->image_path);
            }
        }

        return view('employee.profile.show', compact('user', 'employee', 'photoUrl'));
    }

    public function edit()
    {
        $user = Auth::user();
        $employee = $user->employee;

        return view('employee.profile.edit', compact('user', 'employee'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        $request->validate([
            'name' => 'required|string|max:255',
            'telegram_chat_id' => 'nullable|string|max:50',
            'whatsapp_number' => 'nullable|string|min:8|max:15',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signature_base64' => 'nullable|string',
        ]);

        $user->update([
            'name' => $request->name,
            'telegram_chat_id' => $request->telegram_chat_id,
        ]);

        if ($employee) {
            $employeeData = [
                'whatsapp_number' => $request->whatsapp_number,
            ];

            if ($request->hasFile('photo')) {
                try {
                    $storagePath = $this->telegramStorage->uploadPhoto(
                        $request->file('photo'),
                        "Foto Profil {$employee->full_name} ({$employee->employee_code})",
                    );
                    $employeeData['image_path'] = $storagePath;
                } catch (\Exception $e) {
                    return redirect()->back()->withErrors(['photo' => $e->getMessage()]);
                }
            }

            if ($request->filled('signature_base64')) {
                try {
                    $base64 = $request->input('signature_base64');
                    if (str_contains($base64, ',')) {
                        @[$type, $file_data] = explode(';', $base64);
                        @[, $file_data] = explode(',', $file_data);
                        $image_data = base64_decode($file_data);

                        $manager = new ImageManager(new GdDriver);
                        $image = $manager->read($image_data);
                        $webp = $image->encode(new WebpEncoder(quality: 85));

                        $filename = 'signatures/'.Str::random(40).'.webp';
                        Storage::disk('public')->put($filename, (string) $webp);
                        $employeeData['signature_image'] = $filename;
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to save signature: '.$e->getMessage());
                }
            }

            $employee->update($employeeData);
        }

        return redirect()->route('employee.profile.show')->with('success', 'Profil berhasil diperbarui.');
    }
}
