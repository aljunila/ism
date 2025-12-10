<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class TokenAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization', '');
        if (!str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = substr($authHeader, 7);
        $record = DB::table('user_login')
            ->where('access_token', $token)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Token invalid'], 401);
        }

        // Expired token: bersihkan dan balas 401
        if (Carbon::parse($record->access_token_expires_at)->lte(Carbon::now())) {
            DB::table('user_login')
                ->where('id', $record->id ?? null)
                ->orWhere(function ($q) use ($token) {
                    $q->where('access_token', $token);
                })
                ->update([
                    'access_token' => null,
                    'access_token_expires_at' => null,
                ]);
            return response()->json(['message' => 'Access token expired'], 401);
        }

        $user = User::find($record->user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        Auth::login($user);

        // set context
        session([
            'userid' => $user->id,
            'active_perusahaan_id' => $record->perusahaan_id ?? $user->id_perusahaan,
            'role_id' => $user->role_id ?? $user->id_previllage,
            'active_role_id' => $user->role_id ?? $user->id_previllage,
        ]);

        return $next($request);
    }
}
