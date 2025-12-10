<?php

namespace App\Support;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

class TokenService
{
    public static function issue(User $user, int $accessMinutes = 60, int $refreshDays = 30): array
    {
        $now = Carbon::now();
        $accessToken = Str::uuid()->toString() . '-' . Str::random(32);
        $refreshToken = Str::uuid()->toString() . '-' . Str::random(48);

        $accessExpires = $now->copy()->addMinutes($accessMinutes);
        $refreshExpires = $now->copy()->addDays($refreshDays);

        DB::table('user_login')->updateOrInsert(
            [
                'user_id' => $user->id,
                'perusahaan_id' => session('active_perusahaan_id', $user->id_perusahaan ?? 0),
            ],
            [
                'last_login_at' => $now,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'access_token_expires_at' => $accessExpires,
                'refresh_token_expires_at' => $refreshExpires,
            ]
        );

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'access_token_expires_at' => $accessExpires->toDateTimeString(),
            'refresh_token_expires_at' => $refreshExpires->toDateTimeString(),
        ];
    }

    public static function refresh(User $user, string $refreshToken, int $accessMinutes = 60, int $refreshDays = 30): ?array
    {
        $record = DB::table('user_login')
            ->where('user_id', $user->id)
            ->where('refresh_token', $refreshToken)
            ->where('refresh_token_expires_at', '>', Carbon::now())
            ->first();

        if (!$record) {
            return null;
        }

        return self::issue($user, $accessMinutes, $refreshDays);
    }
}
