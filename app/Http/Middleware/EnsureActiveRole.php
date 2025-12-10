<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\UserCompanyRole;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user && session()->has('userid')) {
            $user = User::find(session('userid'));
        }

        if (!$user) {
            return abort(401, 'Unauthenticated');
        }

        // basic user status guards
        if ((int) ($user->is_delete ?? 0) === 1 || (int) ($user->status ?? 0) === 0) {
            return abort(403, 'User nonaktif');
        }

        $primaryRole = $user->role_id ?? $user->id_previllage;
        // sinkronkan role_id jika masih null tapi ada id_previllage
        if (!$user->role_id && $user->id_previllage) {
            $user->role_id = $user->id_previllage;
            $user->save();
        }

        $activeRoleId = session('active_role_id', $primaryRole);
        $activeCompanyId = session('active_perusahaan_id', $user->id_perusahaan);

        $role = Role::find($activeRoleId);
        if (!$role) {
            return abort(403, 'Role tidak ditemukan');
        }

        // Non-superadmin: pastikan mapping role/perusahaan valid
        if ((int) ($role->is_superadmin ?? 0) !== 1) {
            $hasMapping = false;

            // Role utama (role_id/id_previllage) masih dianggap sah untuk perusahaan utama
            if ((int) $activeRoleId === (int) $primaryRole && (int) $activeCompanyId === (int) $user->id_perusahaan) {
                $hasMapping = true;
            }

            if (!$hasMapping) {
                $hasMapping = UserCompanyRole::where('user_id', $user->id)
                    ->where('perusahaan_id', $activeCompanyId)
                    ->where('role_id', $activeRoleId)
                    ->where('status', 'A')
                    ->exists();
            }

            if (!$hasMapping) {
                return abort(403, 'Role tidak aktif untuk perusahaan ini');
            }
        }

        // share context ke request untuk downstream usage dan sinkronkan session previllage (legacy) ke jenis role
        $previllageLegacy = 4;
        if ($role) {
            if ((int) ($role->is_superadmin ?? 0) === 1) {
                $previllageLegacy = 1;
            } else {
                $jenis = (int) ($role->jenis ?? 0);
                if ($jenis === 1) {
                    $previllageLegacy = 2; // admin perusahaan
                } elseif ($jenis === 2) {
                    $previllageLegacy = 3; // user kapal
                } elseif ($jenis === 3) {
                    $previllageLegacy = 4; // karyawan
                }
            }
        }
        session()->put('previllage', $previllageLegacy);
        $request->attributes->set('active_role_id', $activeRoleId);
        $request->attributes->set('active_role_is_superadmin', (int) ($role->is_superadmin ?? 0) === 1);
        $request->attributes->set('active_perusahaan_id', $activeCompanyId);

        return $next($request);
    }
}
