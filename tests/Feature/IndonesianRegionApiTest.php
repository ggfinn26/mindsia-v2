<?php

it('mengembalikan daftar provinsi Indonesia dari public/data-indonesia', function () {
    $response = $this->getJson('/api/public/indonesia-provinces');

    $response->assertOk();
    expect($response->json())->not->toBeEmpty();
    expect($response->json()[0])->toHaveKeys(['id', 'nama']);
});

it('mengembalikan kota/kabupaten untuk province_id valid', function () {
    $response = $this->getJson('/api/public/indonesia-cities?province_id=11');

    $response->assertOk();
    expect($response->json())->not->toBeEmpty();
    expect($response->json()[0])->toHaveKeys(['id', 'nama']);
});

it('menolak province_id non-numerik', function () {
    $this->getJson('/api/public/indonesia-cities?province_id=abc')
        ->assertOk()
        ->assertExactJson([]);
});
