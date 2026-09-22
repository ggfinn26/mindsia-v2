<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'template_key' => 'member.payment.confirmed',
                'type' => 'email',
                'subject' => 'Konfirmasi Pembayaran Cicilan ke-{{installment_number}} – {{program_name}}',
                'body' => $this->paymentConfirmedBody(),
            ],
        ];

        foreach ($templates as $tpl) {
            NotificationTemplate::updateOrCreate(
                ['template_key' => $tpl['template_key']],
                $tpl,
            );
        }
    }

    private function paymentConfirmedBody(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kwitansi Pembayaran</title>
<style>
  body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 24px; color: #333; }
  .wrapper { max-width: 560px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
  .header { background: #215aac; padding: 28px 32px; text-align: center; }
  .header h1 { color: #fff; font-size: 20px; margin: 0; letter-spacing: .5px; }
  .header p { color: #b8cef0; font-size: 13px; margin: 6px 0 0; }
  .body { padding: 28px 32px; }
  .greeting { font-size: 15px; margin-bottom: 16px; }
  .receipt { background: #f8faff; border: 1px solid #dce8fb; border-radius: 6px; padding: 20px 24px; margin-bottom: 20px; }
  .receipt-title { font-size: 13px; font-weight: bold; text-transform: uppercase; color: #5586DB; letter-spacing: .5px; margin-bottom: 14px; }
  .row { display: flex; justify-content: space-between; font-size: 14px; padding: 6px 0; border-bottom: 1px solid #eef2fb; }
  .row:last-child { border-bottom: none; }
  .label { color: #666; }
  .value { font-weight: 600; color: #1a2a4a; text-align: right; }
  .amount-row { margin-top: 4px; padding-top: 10px; border-top: 2px solid #dce8fb !important; border-bottom: none !important; }
  .amount-row .value { font-size: 16px; color: #215aac; }
  .badge { display: inline-block; background: #d1fae5; color: #065f46; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 20px; margin-bottom: 16px; }
  .note { font-size: 13px; color: #666; margin-bottom: 20px; line-height: 1.6; }
  .cta { text-align: center; margin: 24px 0 16px; }
  .cta a { background: #215aac; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-size: 14px; font-weight: 600; }
  .footer { background: #f4f6f9; padding: 16px 32px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #e8ecf2; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>MINDSIA</h1>
    <p>Kwitansi Pembayaran</p>
  </div>
  <div class="body">
    <p class="greeting">Halo, <strong>{{member_name}}</strong>!</p>
    <span class="badge">✓ Pembayaran Dikonfirmasi</span>

    <div class="receipt">
      <div class="receipt-title">Detail Pembayaran</div>
      <div class="row">
        <span class="label">Program</span>
        <span class="value">{{program_name}}</span>
      </div>
      <div class="row">
        <span class="label">Skema Bayar</span>
        <span class="value">{{payment_plan_label}}</span>
      </div>
      <div class="row">
        <span class="label">Cicilan ke-</span>
        <span class="value">{{installment_number}}</span>
      </div>
      <div class="row">
        <span class="label">Metode Bayar</span>
        <span class="value">{{payment_method_label}}</span>
      </div>
      <div class="row">
        <span class="label">Tanggal Bayar</span>
        <span class="value">{{paid_at}}</span>
      </div>
      <div class="row amount-row">
        <span class="label">Jumlah Dibayar</span>
        <span class="value">Rp {{amount}}</span>
      </div>
    </div>

    <p class="note">
      Total tagihan program Anda adalah <strong>Rp {{final_price}}</strong>.
      {{activation_note}}
    </p>

    <div class="cta">
      <a href="{{dashboard_url}}">Akses Dashboard Member</a>
    </div>
  </div>
  <div class="footer">
    &copy; MINDSIA — Email ini dikirim otomatis, jangan dibalas.
  </div>
</div>
</body>
</html>
HTML;
    }
}
