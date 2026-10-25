<?php

use App\Enums\TestimonialType;
use App\Models\ActivityPhoto;
use App\Models\LandingLeader;
use App\Models\LandingTestimonial;
use App\Models\PhotoCategory;
use App\Models\User;
use App\Services\TelegramStorageService;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    // Ensure permissions exist in test database
    Permission::firstOrCreate(['name' => 'landing.photo.manage', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'landing.leader.manage', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'landing.testimonial.manage', 'guard_name' => 'web']);

    $this->user = User::factory()->create([
        'email_verified_at' => now(),
        'must_change_password' => false,
    ]);
    $this->user->givePermissionTo([
        'landing.photo.manage',
        'landing.leader.manage',
        'landing.testimonial.manage',
    ]);
});

// ─── Public Landing Pages ──────────────────────────────────────────

it('renders home page successfully with status 200', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertViewIs('welcome')
        ->assertSee('MINDSIA');
});

it('renders company profile page successfully with status 200', function () {
    $response = $this->get(route('company.landing'));

    $response->assertOk()
        ->assertViewIs('landing.company.index')
        ->assertSee('Tentang Kami');
});

// ─── Admin Landing Photos ──────────────────────────────────────────

it('renders landing photos index for authorized user', function () {
    ActivityPhoto::create([
        'section' => 'company',
        'title' => 'Foto Kegiatan 1',
        'storage_path' => 'photos/photo1.webp',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('admin.landing.photos.index', ['section' => 'company']));

    $response->assertOk()
        ->assertViewIs('admin.landing.photos.index')
        ->assertSee('Foto Kegiatan 1');
});

it('renders landing photos create form for authorized user', function () {
    $response = $this->actingAs($this->user)
        ->get(route('admin.landing.photos.create', ['section' => 'company']));

    $response->assertOk()
        ->assertViewIs('admin.landing.photos.create')
        ->assertSee('Tambah Foto');
});

it('stores a new landing photo successfully', function () {
    $this->mock(TelegramStorageService::class, function ($mock) {
        $mock->shouldReceive('uploadFile')
            ->once()
            ->andReturn(['file_id' => 'mock_photo_file_id']);
    });

    $category = PhotoCategory::create(['name' => 'Rakernas 2026']);
    $file = UploadedFile::fake()->create('activity.webp', 200, 'image/webp');

    $response = $this->actingAs($this->user)
        ->post(route('admin.landing.photos.store'), [
            'section' => 'company',
            'category_id' => $category->id,
            'title' => 'Foto Baru',
            'caption' => 'Keterangan foto baru',
            'photo' => $file,
            'sort_order' => 1,
            'is_active' => 1,
        ]);

    $response->assertRedirect(route('admin.landing.photos.index', ['section' => 'company']))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('activity_photos', [
        'section' => 'company',
        'category_id' => $category->id,
        'title' => 'Foto Baru',
        'storage_path' => 'mock_photo_file_id',
    ]);
});

it('renders landing photos edit form for authorized user', function () {
    $photo = ActivityPhoto::create([
        'section' => 'company',
        'title' => 'Foto Untuk Diedit',
        'storage_path' => 'photos/edit.webp',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('admin.landing.photos.edit', $photo));

    $response->assertOk()
        ->assertViewIs('admin.landing.photos.edit')
        ->assertSee('Foto Untuk Diedit');
});

it('updates an existing landing photo successfully', function () {
    $photo = ActivityPhoto::create([
        'section' => 'company',
        'title' => 'Foto Lama',
        'caption' => 'Caption Lama',
        'storage_path' => 'photos/old.webp',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $category = PhotoCategory::create(['name' => 'PINE']);

    $response = $this->actingAs($this->user)
        ->put(route('admin.landing.photos.update', $photo), [
            'category_id' => $category->id,
            'title' => 'Foto Terupdate',
            'caption' => 'Caption Terupdate',
            'sort_order' => 2,
            'is_active' => 1,
        ]);

    $response->assertRedirect(route('admin.landing.photos.index', ['section' => 'company']))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('activity_photos', [
        'id' => $photo->id,
        'category_id' => $category->id,
        'title' => 'Foto Terupdate',
        'caption' => 'Caption Terupdate',
        'sort_order' => 2,
    ]);
});

it('deletes a landing photo successfully', function () {
    $photo = ActivityPhoto::create([
        'section' => 'company',
        'title' => 'Foto Hapus',
        'storage_path' => 'photos/delete.webp',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->delete(route('admin.landing.photos.destroy', $photo));

    $response->assertRedirect(route('admin.landing.photos.index', ['section' => 'company']))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('activity_photos', [
        'id' => $photo->id,
    ]);
});

// ─── Admin Landing Leaders ─────────────────────────────────────────

it('renders landing leaders index for authorized user', function () {
    LandingLeader::create([
        'name' => 'John Doe',
        'title' => 'Chief Executive Officer',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('admin.landing.leaders.index'));

    $response->assertOk()
        ->assertViewIs('admin.landing.leaders.index')
        ->assertSee('John Doe')
        ->assertSee('Chief Executive Officer');
});

it('renders landing leaders create form for authorized user', function () {
    $response = $this->actingAs($this->user)
        ->get(route('admin.landing.leaders.create'));

    $response->assertOk()
        ->assertViewIs('admin.landing.leaders.create')
        ->assertSee('Tambah Pemimpin');
});

it('stores a new landing leader successfully', function () {
    $this->mock(TelegramStorageService::class, function ($mock) {
        $mock->shouldReceive('uploadFile')
            ->once()
            ->andReturn(['file_id' => 'mock_leader_photo_id']);
    });

    $file = UploadedFile::fake()->create('leader.webp', 150, 'image/webp');

    $response = $this->actingAs($this->user)
        ->post(route('admin.landing.leaders.store'), [
            'name' => 'Jane Smith',
            'title' => 'Chief Technology Officer',
            'photo' => $file,
            'sort_order' => 1,
            'is_active' => 1,
        ]);

    $response->assertRedirect(route('admin.landing.leaders.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('landing_leaders', [
        'name' => 'Jane Smith',
        'title' => 'Chief Technology Officer',
        'storage_path' => 'mock_leader_photo_id',
    ]);
});

it('renders landing leaders edit form for authorized user', function () {
    $leader = LandingLeader::create([
        'name' => 'Michael Scott',
        'title' => 'Regional Manager',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('admin.landing.leaders.edit', $leader));

    $response->assertOk()
        ->assertViewIs('admin.landing.leaders.edit')
        ->assertSee('Michael Scott');
});

it('updates an existing landing leader successfully', function () {
    $leader = LandingLeader::create([
        'name' => 'Dwight Schrute',
        'title' => 'Assistant to the Regional Manager',
        'sort_order' => 2,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)
        ->put(route('admin.landing.leaders.update', $leader), [
            'name' => 'Dwight K. Schrute',
            'title' => 'Assistant Regional Manager',
            'sort_order' => 1,
            'is_active' => 1,
        ]);

    $response->assertRedirect(route('admin.landing.leaders.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('landing_leaders', [
        'id' => $leader->id,
        'name' => 'Dwight K. Schrute',
        'title' => 'Assistant Regional Manager',
    ]);
});

it('deletes a landing leader successfully', function () {
    $leader = LandingLeader::create([
        'name' => 'Andy Bernard',
        'title' => 'Director of Sales',
        'sort_order' => 3,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)
        ->delete(route('admin.landing.leaders.destroy', $leader));

    $response->assertRedirect(route('admin.landing.leaders.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('landing_leaders', [
        'id' => $leader->id,
    ]);
});

// ─── Admin Landing Testimonials ─────────────────────────────────────

it('renders landing testimonials index for authorized user', function () {
    LandingTestimonial::create([
        'type' => TestimonialType::Text,
        'name' => 'Alumni Hebat',
        'quote' => 'Kursus di MINDSIA sangat membantu karir saya!',
        'program' => 'Intensive Camp',
        'city' => 'Bandung',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('admin.landing.testimonials.index'));

    $response->assertOk()
        ->assertViewIs('admin.landing.testimonials.index')
        ->assertSee('Alumni Hebat')
        ->assertSee('Kursus di MINDSIA sangat membantu karir saya!');
});

it('renders landing testimonials create form for authorized user', function () {
    $response = $this->actingAs($this->user)
        ->get(route('admin.landing.testimonials.create'));

    $response->assertOk()
        ->assertViewIs('admin.landing.testimonials.create')
        ->assertSee('Tambah Testimoni');
});

it('stores a new text testimonial successfully', function () {
    $response = $this->actingAs($this->user)
        ->post(route('admin.landing.testimonials.store'), [
            'type' => 'text',
            'name' => 'Rina Wijaya',
            'quote' => 'Metode 24 HESA sangat menyenangkan dan berdampak besar.',
            'program' => 'English House',
            'city' => 'Jakarta',
            'sort_order' => 1,
            'is_active' => 1,
        ]);

    $response->assertRedirect(route('admin.landing.testimonials.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('landing_testimonials', [
        'name' => 'Rina Wijaya',
        'quote' => 'Metode 24 HESA sangat menyenangkan dan berdampak besar.',
        'program' => 'English House',
        'city' => 'Jakarta',
    ]);
});

it('renders landing testimonials edit form for authorized user', function () {
    $testimonial = LandingTestimonial::create([
        'type' => TestimonialType::Text,
        'name' => 'Budi Santoso',
        'quote' => 'Pengalaman belajar yang berkesan.',
        'program' => 'English Cafe',
        'city' => 'Surabaya',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('admin.landing.testimonials.edit', $testimonial));

    $response->assertOk()
        ->assertViewIs('admin.landing.testimonials.edit')
        ->assertSee('Budi Santoso')
        ->assertSee('Pengalaman belajar yang berkesan.');
});

it('updates an existing landing testimonial successfully', function () {
    $testimonial = LandingTestimonial::create([
        'type' => TestimonialType::Text,
        'name' => 'Siti Nurhaliza',
        'quote' => 'Quote Lama',
        'program' => 'English House',
        'city' => 'Semarang',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->put(route('admin.landing.testimonials.update', $testimonial), [
            'name' => 'Siti Nurhaliza',
            'quote' => 'Quote Baru yang lebih mendalam.',
            'program' => 'English House & Cafe',
            'city' => 'Semarang',
            'sort_order' => 2,
            'is_active' => 1,
        ]);

    $response->assertRedirect(route('admin.landing.testimonials.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('landing_testimonials', [
        'id' => $testimonial->id,
        'quote' => 'Quote Baru yang lebih mendalam.',
        'program' => 'English House & Cafe',
        'sort_order' => 2,
    ]);
});

it('deletes a landing testimonial successfully', function () {
    $testimonial = LandingTestimonial::create([
        'type' => TestimonialType::Text,
        'name' => 'Ahmad Fauzi',
        'quote' => 'Akan dihapus',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->delete(route('admin.landing.testimonials.destroy', $testimonial));

    $response->assertRedirect(route('admin.landing.testimonials.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('landing_testimonials', [
        'id' => $testimonial->id,
    ]);
});
