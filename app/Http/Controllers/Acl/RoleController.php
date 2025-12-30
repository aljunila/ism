<?php

namespace App\Http\Controllers\Acl;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleMenu;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function data()
    {
        $query = Role::query()->orderBy('id');
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return view('acl.partials.role_actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function all()
    {
        return Role::where('status', 'A')->get(['id', 'kode', 'nama']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:30',
            'nama' => 'required|string|max:50',
            'status' => 'required|in:A,D',
            'jenis' => 'required|integer|max:50',
            'is_superadmin' => 'nullable|boolean',
        ]);
        $validated['is_superadmin'] = $request->boolean('is_superadmin');
        Role::create($validated);
        return response()->json(['message' => 'Role ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:30',
            'nama' => 'required|string|max:50',
            'status' => 'required|in:A,D',
            'jenis' => 'required|integer|max:50',
            'is_superadmin' => 'nullable|boolean',
        ]);
        $role = Role::findOrFail($id);
        $validated['is_superadmin'] = $request->boolean('is_superadmin');
        $role->update($validated);
        return response()->json(['message' => 'Role diperbarui']);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return response()->json(['message' => 'Role dihapus']);
    }

    public function getRoleMenus($roleId)
    {
        return RoleMenu::where('role_id', $roleId)->pluck('menu_id');
    }

    public function mapMenu(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|integer',
            'menu_ids' => 'array',
            'menu_ids.*' => 'integer',
        ]);

        RoleMenu::where('role_id', $validated['role_id'])->delete();
        $insert = [];
        foreach ($validated['menu_ids'] ?? [] as $menuId) {
            $insert[] = [
                'role_id' => $validated['role_id'],
                'menu_id' => $menuId,
            ];
        }
        if (!empty($insert)) {
            RoleMenu::insert($insert);
        }

        return response()->json(['message' => 'Mapping menu disimpan']);
    }
}
