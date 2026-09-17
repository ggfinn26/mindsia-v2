<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberDataRequest;
use App\Http\Requests\Member\UpdateMemberDataRequest;
use App\Models\MemberAccount;
use App\Models\MemberData;
use App\Repositories\Member\MemberDataRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MemberDataController extends Controller
{
    public function __construct(
        private readonly MemberDataRepository $repo,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['institution_id', 'program_id', 'search']);

        return view('admin.member.index', [
            'members' => $this->repo->paginate($filters),
        ]);
    }

    public function create(): View
    {
        return view('admin.member.create');
    }

    public function store(StoreMemberDataRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $createAccount = (bool) ($validated['create_account'] ?? false);
        unset($validated['create_account']);

        $member = $this->repo->create($validated);

        if ($createAccount && $member->email) {
            $account = MemberAccount::firstOrCreate(
                ['email' => $member->email],
                ['members_data_id' => $member->id, 'password' => Str::random(12)],
            );
            $account->sendEmailVerificationNotification();
        }

        return redirect()->route('members.show', $member)->with('success', 'Data member berhasil ditambahkan.');
    }

    public function show(MemberData $member): View
    {
        return view('admin.member.show', [
            'member' => $this->repo->findWithDetails($member->id),
        ]);
    }

    public function edit(MemberData $member): View
    {
        return view('admin.member.edit', compact('member'));
    }

    public function update(UpdateMemberDataRequest $request, MemberData $member): RedirectResponse
    {
        $this->repo->update($member, $request->validated());

        return redirect()->route('members.show', $member)->with('success', 'Data member berhasil diperbarui.');
    }

    public function activate(MemberData $member): RedirectResponse
    {
        abort_unless(auth()->user()->can('member.manage'), 403);

        $member->update(['activation_status' => 'active']);

        return back()->with('success', 'Member berhasil diaktifkan.');
    }

    public function destroy(MemberData $member): RedirectResponse
    {
        abort_unless(auth()->user()->can('member.manage'), 403);

        try {
            $this->repo->delete($member);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('members.index')->with('success', 'Data member berhasil dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $importedCount = 0;

        // Clean headers (trim and lowercase)
        $header = array_map(fn ($col) => trim(strtolower($col)), $header);

        DB::transaction(function () use ($handle, $header, &$importedCount) {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($header) !== count($row)) {
                    continue; // Skip invalid rows
                }

                $data = array_combine($header, $row);
                $data = array_map('trim', $data);

                // Required minimal data
                if (empty($data['full_name'])) {
                    continue;
                }

                // Prepare MemberData
                $memberDataParams = [
                    'full_name' => $data['full_name'],
                    'gender' => in_array($data['gender'] ?? '', ['L', 'P']) ? $data['gender'] : null,
                    'birthdate' => ! empty($data['birthdate']) ? $data['birthdate'] : null, // Assuming YYYY-MM-DD
                    'whatsapp_number' => $data['whatsapp_number'] ?? null,
                    'email' => $data['email'] ?? null,
                    'institution_id' => ! empty($data['institution_id']) ? $data['institution_id'] : null,
                    'program_id' => ! empty($data['program_id']) ? $data['program_id'] : null,
                ];

                $member = MemberData::create(array_filter($memberDataParams, fn ($v) => ! is_null($v)));
                $importedCount++;

                // Auto-create account if requested and email is present
                $autoCreate = in_array(strtolower($data['auto_create_account'] ?? ''), ['1', 'yes', 'y', 'true']);

                if ($autoCreate && ! empty($member->email)) {
                    // Random secure password since they need to reset or will be given default
                    $password = Str::random(12);

                    MemberAccount::firstOrCreate(
                        ['email' => $member->email],
                        [
                            'members_data_id' => $member->id,
                            'password' => Hash::make($password),
                        ]
                    );
                }
            }
        });

        fclose($handle);

        return redirect()->route('members.index')->with('success', "Berhasil mengimpor {$importedCount} data member.");
    }
}
