<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Perusahaan;
use App\Models\Role;
use App\Models\UserCompanyRole;
use Illuminate\Support\Facades\DB;

class ActiveContextController extends Controller
{
    public function options(Request $request)
    {
        $user = $request->user();
        $options = [];

        // Role utama + perusahaan utama
        $mainRoleId = $user->role_id ?? $user->id_previllage;
        $mainRole = Role::find($mainRoleId);
        $isSuper = $mainRole && (int) ($mainRole->is_superadmin ?? 0) === 1;

        if ($isSuper) {
            // superadmin bisa pilih semua perusahaan, role tetap role superadmin
            $perusahaans = Perusahaan::all(['id', 'nama']);
            foreach ($perusahaans as $p) {
                $options[] = [
                    'perusahaan_id' => $p->id,
                    'perusahaan_nama' => $p->nama,
                    'role_id' => $mainRoleId,
                    'role_nama' => $mainRole->nama ?? 'Superadmin',
                    'is_superadmin' => 1,
                ];
            }
        } else {
            if ($user->id_perusahaan) {
                $perusahaan = Perusahaan::find($user->id_perusahaan);
                if ($perusahaan) {
                    $options[] = [
                        'perusahaan_id' => $perusahaan->id,
                        'perusahaan_nama' => $perusahaan->nama,
                        'role_id' => $mainRoleId,
                        'role_nama' => $mainRole->nama ?? '',
                        'is_superadmin' => 0,
                    ];
                }
            }

            $extras = UserCompanyRole::where('user_id', $user->id)
                ->with('role:id,nama,is_superadmin', 'perusahaan:id,nama')
                ->get();

            foreach ($extras as $ex) {
                if (!$ex->perusahaan) {
                    continue;
                }
                $options[] = [
                    'perusahaan_id' => $ex->perusahaan_id,
                    'perusahaan_nama' => $ex->perusahaan->nama,
                    'role_id' => $ex->role_id,
                    'role_nama' => $ex->role->nama ?? '',
                    'is_superadmin' => (int) ($ex->role->is_superadmin ?? 0),
                ];
            }
        }

        // unique by perusahaan+role
        $options = collect($options)
            ->unique(fn ($item) => $item['perusahaan_id'] . '-' . $item['role_id'])
            ->values()
            ->all();

        $activePerusahaan = Session::get('active_perusahaan_id');
        $activeRole = Session::get('active_role_id');
        $requireSelection = count($options) > 1 && !($activePerusahaan && $activeRole);

        return response()->json([
            'requireSelection' => $requireSelection,
            'options' => $options,
            'active_perusahaan_id' => $activePerusahaan,
            'active_role_id' => $activeRole,
        ]);
    }

    public function set(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'perusahaan_id' => 'required|integer',
            'role_id' => 'required|integer',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $isSuper = (int) ($role->is_superadmin ?? 0) === 1;

        if (!$isSuper) {
            $valid = false;
            if ((int) $mainRoleId === (int) $validated['role_id'] && (int) $user->id_perusahaan === (int) $validated['perusahaan_id']) {
                $valid = true;
            }
            if (!$valid) {
                $valid = UserCompanyRole::where('user_id', $user->id)
                    ->where('perusahaan_id', $validated['perusahaan_id'])
                    ->where('role_id', $validated['role_id'])
                    ->exists();
            }
            if (!$valid) {
                return response()->json(['message' => 'Mapping role/perusahaan tidak valid'], 422);
            }
        }

        Session::put('active_perusahaan_id', $validated['perusahaan_id']);
        Session::put('active_role_id', $validated['role_id']);

        // catat ke user_login
        DB::table('user_login')->updateOrInsert(
            [
                'user_id' => $user->id,
                'perusahaan_id' => $validated['perusahaan_id'],
            ],
            [
                'last_login_at' => now(),
                'access_token' => null,
                'refresh_token' => null,
                'access_token_expires_at' => null,
                'refresh_token_expires_at' => null,
            ]
        );

        return response()->json(['message' => 'Context aktif disimpan']);
    }
}
