<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class AttendanceRecapController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:attendance.document_signature.manage'),
        ];
    }

    // ponytail: stub — full implementation when views are built
    public function index(): View
    {
        return view('admin.attendance.recap.index');
    }

    public function show(int $id): View
    {
        return view('admin.attendance.recap.show', ['id' => $id]);
    }
}
