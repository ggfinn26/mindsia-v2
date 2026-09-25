<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class PositionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:position.manage'),
        ];
    }

    public function index(): View
    {
        $positions = Position::with('role')->paginate(15);

        return view('admin.positions.index', compact('positions'));
    }

    public function create(): View
    {
        $roles = Role::all();

        return view('admin.positions.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'position_name' => ['required', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'hierarchy_order' => ['required', 'integer'],
        ]);

        Position::create($validated);

        return redirect()->route('positions.index')->with('success', 'Posisi berhasil ditambahkan.');
    }

    public function edit(Position $position): View
    {
        $position->load('role');
        $roles = Role::all();

        return view('admin.positions.edit', compact('position', 'roles'));
    }

    public function update(Request $request, Position $position): RedirectResponse
    {
        $validated = $request->validate([
            'position_name' => ['required', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'hierarchy_order' => ['required', 'integer'],
        ]);

        $position->update($validated);

        return redirect()->route('positions.index')->with('success', 'Posisi berhasil diperbarui.');
    }

    public function destroy(Position $position): RedirectResponse
    {
        if (! $position->canDelete()) {
            return redirect()->route('positions.index')->with('error', 'Posisi tidak bisa dihapus karena masih ada karyawan aktif dengan posisi ini.');
        }

        $position->delete();

        return redirect()->route('positions.index')->with('success', 'Posisi berhasil dihapus.');
    }
}
