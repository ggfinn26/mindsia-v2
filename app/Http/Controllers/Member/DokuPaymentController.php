<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberPayment;
use App\Services\Doku\DokuCheckoutService;
use Illuminate\Http\RedirectResponse;

class DokuPaymentController extends Controller
{
    public function __construct(private readonly DokuCheckoutService $checkout) {}

    public function pay(MemberPayment $memberPayment): RedirectResponse
    {
        $membersDataId = auth('member')->user()?->memberData?->id;

        abort_unless(
            $membersDataId !== null && $memberPayment->registration->members_data_id === $membersDataId,
            403,
        );

        abort_if($memberPayment->payment_status === 'paid', 422, 'Cicilan sudah dibayar.');

        $url = $this->checkout->createCheckout($memberPayment);

        return redirect()->away($url);
    }
}
