<?php

namespace App\Http\Middleware;

use App\Enums\Member\ActivationStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMemberIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $memberData = auth('member')->user()?->memberData;

        if (! $memberData) {
            return redirect()->route('member.dashboard');
        }

        $status = $memberData->activation_status;

        if ($status === ActivationStatus::Ditangguhkan) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun Anda ditangguhkan. Selesaikan pembayaran yang tertunggak untuk memulihkan akses.'], 403);
            }

            return response()->view('member.activation.status', [
                'status' => 'ditangguhkan',
                'message' => 'Akun Anda ditangguhkan. Selesaikan pembayaran yang tertunggak untuk memulihkan akses.',
            ]);
        }

        if ($status !== ActivationStatus::Aktif) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun Anda belum aktif. Selesaikan pembayaran pertama untuk mengakses fitur ini.'], 403);
            }

            return response()->view('member.activation.status', [
                'status' => 'pending',
                'message' => 'Akun Anda belum aktif. Selesaikan pembayaran pertama untuk mengakses fitur ini.',
            ]);
        }

        return $next($request);
    }
}
