<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user && session()->has('userid')) {
            $user = \App\Models\User::find(session('userid'));
        }
        if (!$user) {
            return abort(401, 'Unauthenticated');
        }

        $roleId = session('active_role_id', $user->role_id ?? $user->id_previllage);
        $role = Role::find($roleId);
        $isSuper = $role && (int) ($role->is_superadmin ?? 0) === 1;
        if ($isSuper) {
            return $next($request);
        }

        $path = ltrim($request->path(), '/');
        $isApiRequest = str_starts_with($path, 'api/');

        // whitelist routes always allowed
        $whitelist = [
            '',
            'dashboard',
            'logout',
            'active-context/options',
            'active-context/set',
        ];
        foreach ($whitelist as $allow) {
            if ($path === trim($allow, '/')) {
                return $next($request);
            }
        }

        $allowedLinks = DB::table('role_menu')
            ->leftJoin('menu', 'menu.id', '=', 'role_menu.menu_id')
            ->where('role_menu.role_id', $roleId)
            ->where('menu.status', 'A')
            ->pluck('menu.link')
            ->filter()
            ->map(function ($link) {
                return ltrim($link, '/');
            })
            ->toArray();

        $allowed = false;

        // Cek akses untuk halaman/menu utama
        foreach ($allowedLinks as $link) {
            if ($link === '') {
                continue;
            }
            if (str_starts_with($path, $link)) {
                $allowed = true;
                break;
            }
        }

        // Binding khusus untuk endpoint API yang dipakai oleh halaman ACL
        if (!$allowed && $isApiRequest) {
            $apiBindings = [
                'api/perusahaan/all' => ['acl/users', 'acl/roles', 'acl/menu'],
                'api/karyawan/all' => ['acl/users', 'karyawan'],
            ];

            foreach ($apiBindings as $apiPath => $requiredMenus) {
                if (!str_starts_with($path, $apiPath)) {
                    continue;
                }
                foreach ($requiredMenus as $required) {
                    if (in_array(ltrim($required, '/'), $allowedLinks, true)) {
                        $allowed = true;
                        break 2;
                    }
                }
            }
        }

        if (!$allowed) {
            return abort(403, 'Akses menu tidak diizinkan');
        }

        return $next($request);
    }
}
