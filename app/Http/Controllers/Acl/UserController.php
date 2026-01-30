<?php

namespace App\Http\Controllers\Acl;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Perusahaan;
use App\Models\Karyawan;
use App\Models\UserCompanyRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function data()
    {
        $query = User::where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('role', function ($row) {
                $role = Role::find($row->role_id ?? $row->id_previllage);
                return $role ? $role->nama : '-';
            })
            ->addColumn('perusahaan', function ($row) {
                $perusahaan = Perusahaan::find($row->id_perusahaan);
                return $perusahaan ? $perusahaan->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('acl.partials.user_actions', compact('row'))->render();
            })
            ->addColumn('status_toggle', function ($row) {
                $checked = $row->status ? 'checked' : '';
                return '<div class="form-check form-switch">
                            <input class="form-check-input toggle-user-status" type="checkbox" data-id="'.$row->id.'" '.$checked.'>
                        </div>';
            })
            ->rawColumns(['aksi', 'status_toggle'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:user,username',
            'password' => 'required|string|min:6',
            'nama' => 'required|string|max:50',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'integer',
            'perusahaan_id' => 'nullable|integer',
            'karyawan_id' => 'nullable|integer',
            'additional' => 'array',
            'additional.*.perusahaan_id' => 'required_with:additional|integer',
            'additional.*.role_ids' => 'required_with:additional|array',
            'additional.*.role_ids.*' => 'integer',
        ]);

        $roleIds = $validated['role_ids'];
        $mainRoleId = $roleIds[0];
        $role = Role::findOrFail($mainRoleId);

        $perusahaanId = $role->is_superadmin ? 0 : ($validated['perusahaan_id'] ?? 0);
        $karyawanId = $role->is_superadmin ? 0 : ($validated['karyawan_id'] ?? 0);

        $user = User::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'nama' => $validated['nama'],
            'id_previllage' => $role->jenis, // legacy mirror
            'role_id' => $role->id,
            'id_perusahaan' => $perusahaanId,
            'id_karyawan' => $karyawanId,
            'status' => 1,
            'active_at' => now(),
        ]);

        $additional = $validated['additional'] ?? [];

        // tambahan role utama (selain main) ke perusahaan utama
        if (!$role->is_superadmin && count($roleIds) > 1) {
            foreach (array_slice($roleIds, 1) as $rid) {
                UserCompanyRole::create([
                    'user_id' => $user->id,
                    'perusahaan_id' => $perusahaanId,
                    'role_id' => $rid,
                    'status' => 'A',
                ]);
            }
        }

        // additional mapping to user_company_roles
        foreach ($additional as $item) {
            foreach ($item['role_ids'] as $rid) {
                UserCompanyRole::create([
                    'user_id' => $user->id,
                    'perusahaan_id' => $item['perusahaan_id'],
                    'role_id' => $rid,
                    'status' => 'A',
                ]);
            }
        }

        return response()->json(['message' => 'User berhasil ditambahkan']);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $mainExtraRoles = UserCompanyRole::where('user_id', $id)
            ->where('perusahaan_id', $user->id_perusahaan)
            ->pluck('role_id')
            ->toArray();
        $primaryRole = $user->role_id ?? $user->id_previllage;
        $roles = collect([$primaryRole])->merge($mainExtraRoles)->unique()->values();

        $additional = UserCompanyRole::where('user_id', $id)
            ->when($user->id_perusahaan, function ($q) use ($user) {
                $q->where('perusahaan_id', '!=', $user->id_perusahaan);
            })
            ->get()
            ->groupBy('perusahaan_id')
            ->map(function ($items) {
                return [
                    'perusahaan_id' => $items->first()->perusahaan_id,
                    'role_ids' => $items->pluck('role_id')->unique()->values(),
                ];
            })
            ->values();

        return response()->json([
            'user' => $user,
            'role_ids' => $roles,
            'additional' => $additional,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'password' => 'nullable|string|min:6',
            'nama' => 'required|string|max:50',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'integer',
            'perusahaan_id' => 'nullable|integer',
            'karyawan_id' => 'nullable|integer',
            'additional' => 'array',
            'additional.*.perusahaan_id' => 'required_with:additional|integer',
            'additional.*.role_ids' => 'required_with:additional|array',
            'additional.*.role_ids.*' => 'integer',
        ]);

        $user = User::findOrFail($id);

        $roleIds = $validated['role_ids'];
        $mainRoleId = $roleIds[0];
        $role = Role::findOrFail($mainRoleId);

        $perusahaanId = $role->is_superadmin ? 0 : ($validated['perusahaan_id'] ?? 0);
        $karyawanId = $role->is_superadmin ? 0 : ($validated['karyawan_id'] ?? 0);

        $user->nama = $validated['nama'];
        $user->id_previllage = $role->jenis; // legacy mirror
        $user->role_id = $mainRoleId;
        $user->id_perusahaan = $perusahaanId;
        $user->id_karyawan = $karyawanId;
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        UserCompanyRole::where('user_id', $id)->delete();

        if (!$role->is_superadmin && count($roleIds) > 1) {
            foreach (array_slice($roleIds, 1) as $rid) {
                UserCompanyRole::create([
                    'user_id' => $user->id,
                    'perusahaan_id' => $perusahaanId,
                    'role_id' => $rid,
                    'status' => 'A',
                ]);
            }
        }

        $additional = $validated['additional'] ?? [];
        foreach ($additional as $item) {
            foreach ($item['role_ids'] as $rid) {
                UserCompanyRole::create([
                    'user_id' => $user->id,
                    'perusahaan_id' => $item['perusahaan_id'],
                    'role_id' => $rid,
                    'status' => 'A',
                ]);
            }
        }

        return response()->json(['message' => 'User berhasil diperbarui']);
    }

    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $newStatus = $request->boolean('status');
        $user->status = $newStatus ? 1 : 0;
        $user->active_at = $newStatus ? now() : $user->active_at;
        $user->unactive_at = $newStatus ? $user->unactive_at : now();
        $user->save();

        return response()->json(['message' => 'Status user diperbarui']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->is_delete = 1;
        $user->status = 0;
        $user->deleted_at = now();
        $user->deleted_by = auth()->id();
        $user->save();

        return response()->json(['message' => 'User dihapus (soft delete)']);
    }
}
