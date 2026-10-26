<?php

use App\Services\Doku\DokuSignature;

it('menghasilkan signature dengan prefix HMACSHA256 dan Digest untuk POST', function () {
    $sig = new DokuSignature('CLIENT-X', 'secret-key');
    $out = $sig->sign('req-1', '2026-10-25T00:00:00Z', '/checkout/v1/payment', '{"order":{"amount":1000}}');

    expect($out)->toStartWith('HMACSHA256=');
    // digest mengubah signature: body beda -> signature beda
    $other = $sig->sign('req-1', '2026-10-25T00:00:00Z', '/checkout/v1/payment', '{"order":{"amount":2000}}');
    expect($out)->not->toBe($other);
});

it('verify menerima signature yang cocok dan menolak yang salah', function () {
    $sig = new DokuSignature('CLIENT-X', 'secret-key');
    $target = '/webhooks/doku';
    $body = '{"transaction":{"status":"SUCCESS"}}';
    $valid = $sig->sign('req-9', '2026-10-25T00:00:00Z', $target, $body);

    expect($sig->verify($valid, 'req-9', '2026-10-25T00:00:00Z', $target, $body))->toBeTrue();
    expect($sig->verify($valid, 'req-9', '2026-10-25T00:00:00Z', $target, '{"tampered":1}'))->toBeFalse();

    $wrongSecret = new DokuSignature('CLIENT-X', 'other-secret');
    expect($wrongSecret->verify($valid, 'req-9', '2026-10-25T00:00:00Z', $target, $body))->toBeFalse();
});

it('menolak verify jika kredensial kosong (anti-forgery saat misconfigurasi)', function () {
    $target = '/webhooks/doku';
    $body = '{"transaction":{"status":"SUCCESS"}}';

    // Attacker menandatangani dengan secret kosong, meniru deploy tanpa DOKU_SECRET_KEY.
    $forged = (new DokuSignature('', ''))->sign('req-9', '2026-10-25T00:00:00Z', $target, $body);

    expect((new DokuSignature('', ''))->verify($forged, 'req-9', '2026-10-25T00:00:00Z', $target, $body))->toBeFalse();
    expect((new DokuSignature('CLIENT-X', ''))->verify($forged, 'req-9', '2026-10-25T00:00:00Z', $target, $body))->toBeFalse();
});

it('GET tanpa body tidak menyertakan Digest', function () {
    $sig = new DokuSignature('CLIENT-X', 'secret-key');
    $withBody = $sig->sign('req-1', '2026-10-25T00:00:00Z', '/orders/v1/status/INV-1', '');
    $noBody = $sig->sign('req-1', '2026-10-25T00:00:00Z', '/orders/v1/status/INV-1', null);

    expect($noBody)->not->toBe($withBody);
});
