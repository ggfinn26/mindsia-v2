<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PositionController extends Controller
{
    public function __construct()
    {
        $this->middleware('board-of-directors');
    }

    public function index(): View
    {
        $positions = Position::with('role')->paginate(15);

        return view('admin.positions.index', compact('positions'));
    }

    public function create(): View
    {
        $roles = Role::all();
        $permissions = Permission::orderBy('name')->get();

        return view('admin.positions.create', compact('roles', 'permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'position_name' => ['required', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'hierarchy_order' => ['required', 'integer'],
            'permission_ids' => ['array', 'exists:permissions,id'],
        ]);

        $position = Position::create($validated);
        $position->permissions()->sync($request->input('permission_ids', []));

        return redirect()->route('positions.index')->with('success', 'Posisi berhasil ditambahkan.');
    }

    public function edit(Position $position): View
    {
        $position->load('role', 'permissions');
        $roles = Role::all();
        $permissions = Permission::orderBy('name')->get();

        return view('admin.positions.edit', compact('position', 'roles', 'permissions'));
    }

    public function update(Request $request, Position $position): RedirectResponse
    {
        $validated = $request->validate([
            'position_name' => ['required', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'hierarchy_order' => ['required', 'integer'],
            'permission_ids' => ['array', 'exists:permissions,id'],
        ]);

        $position->update($validated);
        $position->permissions()->sync($request->input('permission_ids', []));

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
