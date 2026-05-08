<?php

namespace App\Services;

use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class NotificationService
{
    public function sendToTargets(array $payload): array
    {
        if (!Schema::hasTable('t_notifikasi')) {
            return ['count' => 0, 'user_ids' => []];
        }

        $userIds = $this->resolveRecipients($payload);
        $createdBy = $payload['created_by'] ?? null;
        $createdIds = [];

        foreach ($userIds as $userId) {
            $notification = Notifikasi::create([
                'uid' => Str::uuid()->toString(),
                'id_user' => $userId,
                'tipe' => $payload['tipe'] ?? 'info',
                'judul' => $payload['judul'],
                'pesan' => $payload['pesan'] ?? null,
                'url' => $payload['url'] ?? null,
                'is_delete' => 0,
                'created_by' => $createdBy,
                'created_date' => date('Y-m-d H:i:s'),
            ]);

            $createdIds[] = $notification->id;
        }

        return [
            'count' => count($createdIds),
            'ids' => $createdIds,
            'user_ids' => $userIds,
        ];
    }

    public function resolveRecipients(array $payload): array
    {
        $userIds = collect();

        foreach ($this->normalizeIds($payload['user_ids'] ?? []) as $id) {
            $userIds->push($id);
        }

        foreach ($this->normalizeIds($payload['id_users'] ?? []) as $id) {
            $userIds->push($id);
        }

        if (!empty($payload['user_id'])) {
            $userIds->push((int) $payload['user_id']);
        }

        if (!empty($payload['id_user'])) {
            $userIds->push((int) $payload['id_user']);
        }

        $roleIds = collect($this->normalizeIds($payload['role_ids'] ?? []));
        $roleIds = $roleIds->merge($this->normalizeIds($payload['id_roles'] ?? []));

        if (!empty($payload['role_id'])) {
            $roleIds->push((int) $payload['role_id']);
        }

        if (!empty($payload['id_role'])) {
            $roleIds->push((int) $payload['id_role']);
        }

        $roleIds = $roleIds->filter()->unique()->values()->all();
        if (!empty($roleIds)) {
            $userIds = $userIds->merge($this->userIdsByRoles(
                $roleIds,
                $payload['id_perusahaan'] ?? null,
                $payload['id_kapal'] ?? null
            ));
        }

        return $userIds
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function userIdsByRoles(array $roleIds, $idPerusahaan = null, $idKapal = null): array
    {
        if (!Schema::hasTable('user')) {
            return [];
        }

        $userIds = $this->activeUserBaseQuery()
            ->whereIn('user.role_id', $roleIds)
            ->when($idPerusahaan !== null && $idPerusahaan !== '', function ($query) use ($idPerusahaan) {
                $query->where('user.id_perusahaan', (int) $idPerusahaan);
            })
            ->when($idKapal !== null && $idKapal !== '', function ($query) use ($idKapal) {
                $query->where('user.id_kapal', (int) $idKapal);
            })
            ->pluck('user.id');

        if (Schema::hasTable('user_company_roles')) {
            $additionalQuery = $this->activeUserBaseQuery()
                ->join('user_company_roles as ucr', 'ucr.user_id', '=', 'user.id')
                ->where('ucr.status', 'A')
                ->whereIn('ucr.role_id', $roleIds)
                ->when($idPerusahaan !== null && $idPerusahaan !== '', function ($query) use ($idPerusahaan) {
                    $query->where('ucr.perusahaan_id', (int) $idPerusahaan);
                })
                ->when($idKapal !== null && $idKapal !== '', function ($query) use ($idKapal) {
                    $query->where(function ($subQuery) use ($idKapal) {
                        $subQuery->where('ucr.id_kapal', (int) $idKapal)
                            ->orWhere('user.id_kapal', (int) $idKapal);
                    });
                });

            $userIds = $userIds->merge($additionalQuery->pluck('user.id'));
        }

        return $userIds
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function activeUserBaseQuery()
    {
        $query = DB::table('user')->select('user.id');

        if (Schema::hasColumn('user', 'is_delete')) {
            $query->where('user.is_delete', 0);
        }

        if (Schema::hasColumn('user', 'status')) {
            $query->where(function ($q) {
                $q->where('user.status', 1)->orWhere('user.status', 'A');
            });
        }

        return $query;
    }

    private function normalizeIds($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_array($value)) {
            return collect($value)
                ->filter(fn ($id) => $id !== null && $id !== '')
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->values()
                ->all();
        }

        return [(int) $value];
    }
}
