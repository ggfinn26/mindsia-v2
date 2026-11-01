<?php

namespace Tests\Feature\Attendance;

use App\Models\DocumentSignatureSetting;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentSignatureSettingTest extends TestCase
{
    private User $authorizedUser;

    private User $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorizedUser = User::factory()->create();
        $this->authorizedUser->givePermissionTo('attendance.document_signature.manage');

        $this->unauthorizedUser = User::factory()->create();
        $this->unauthorizedUser->assignRole('Tutor');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('attendance.document-signature.index'))
            ->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_is_forbidden(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->get(route('attendance.document-signature.index'))
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_document_signature_index(): void
    {
        DocumentSignatureSetting::create([
            'document_type' => 'SP',
            'signer_name' => 'Budi Santoso',
            'signer_title' => 'Direktur Utama',
        ]);

        $response = $this->actingAs($this->authorizedUser)
            ->get(route('attendance.document-signature.index'));

        $response->assertOk()
            ->assertViewIs('attendance.document-signature.index')
            ->assertSee('Pengaturan Tanda Tangan Dokumen')
            ->assertSee('Budi Santoso')
            ->assertSee('Direktur Utama');
    }

    public function test_authorized_user_can_save_signature_setting_for_sp(): void
    {
        $response = $this->actingAs($this->authorizedUser)
            ->put(route('attendance.document-signature.upsert'), [
                '_active_type' => 'SP',
                'signers' => [
                    'SP' => [
                        'signer_name' => 'Dr. H. Rahmat Hidayat, M.M.',
                        'signer_title' => 'Direktur Operasional',
                    ],
                ],
            ]);

        $response->assertRedirect(route('attendance.document-signature.index'))
            ->assertSessionHas('success', 'Pengaturan tanda tangan berhasil disimpan.')
            ->assertSessionHas('active_type', 'SP');

        $this->assertDatabaseHas('document_signature_settings', [
            'document_type' => 'SP',
            'signer_name' => 'Dr. H. Rahmat Hidayat, M.M.',
            'signer_title' => 'Direktur Operasional',
        ]);
    }

    public function test_authorized_user_can_save_signature_setting_for_kwitansi(): void
    {
        $response = $this->actingAs($this->authorizedUser)
            ->put(route('attendance.document-signature.upsert'), [
                '_active_type' => 'kwitansi',
                'signers' => [
                    'kwitansi' => [
                        'signer_name' => 'Siti Nurhaliza, S.Ak.',
                        'signer_title' => 'Head of Finance',
                    ],
                ],
            ]);

        $response->assertRedirect(route('attendance.document-signature.index'))
            ->assertSessionHas('success', 'Pengaturan tanda tangan berhasil disimpan.')
            ->assertSessionHas('active_type', 'kwitansi');

        $this->assertDatabaseHas('document_signature_settings', [
            'document_type' => 'kwitansi',
            'signer_name' => 'Siti Nurhaliza, S.Ak.',
            'signer_title' => 'Head of Finance',
        ]);
    }

    public function test_save_fails_if_signer_name_is_missing(): void
    {
        $response = $this->actingAs($this->authorizedUser)
            ->put(route('attendance.document-signature.upsert'), [
                'signers' => [
                    'SP' => [
                        'signer_name' => '',
                        'signer_title' => 'Direktur',
                    ],
                ],
            ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('signers.SP.signer_name');
    }

    public function test_save_fails_if_signer_title_is_missing(): void
    {
        $response = $this->actingAs($this->authorizedUser)
            ->put(route('attendance.document-signature.upsert'), [
                'signers' => [
                    'SP' => [
                        'signer_name' => 'Budi',
                        'signer_title' => '',
                    ],
                ],
            ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('signers.SP.signer_title');
    }

    public function test_save_fails_if_document_type_is_invalid(): void
    {
        $response = $this->actingAs($this->authorizedUser)
            ->put(route('attendance.document-signature.upsert'), [
                'signers' => [
                    'tipe_dokumen_palsu' => [
                        'signer_name' => 'Test',
                        'signer_title' => 'Test Title',
                    ],
                ],
            ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('signers');
    }
}
