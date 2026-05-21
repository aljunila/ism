<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Session;

class NotificationController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('t_notifikasi')) {
            return response()->json([
                'unread_count' => 0,
                'notifications' => [],
            ]);
        }

        $userId = (int) Session::get('userid');
        $baseQuery = Notifikasi::where('id_user', $userId)
            ->where('is_delete', 0);

        $unreadCount = (clone $baseQuery)
            ->whereNull('read_at')
            ->count();

        $notifications = (clone $baseQuery)
            ->orderByRaw('CASE WHEN read_at IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('created_date')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'tipe' => $row->tipe,
                    'judul' => $row->judul,
                    'pesan' => $row->pesan,
                    'url' => $row->url,
                    'created_date' => $row->created_date,
                    'is_read' => !is_null($row->read_at),
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function markRead(Request $request)
    {
        if (!Schema::hasTable('t_notifikasi')) {
            return response()->json(['success' => true, 'unread_count' => 0]);
        }

        $userId = (int) Session::get('userid');
        $query = Notifikasi::where('id_user', $userId)
            ->where('is_delete', 0)
            ->whereNull('read_at');

        if ($request->filled('id')) {
            $query->where('id', (int) $request->input('id'));
        }

        $query->update([
            'read_at' => date('Y-m-d H:i:s'),
            'changed_by' => $userId ?: null,
            'changed_date' => date('Y-m-d H:i:s'),
        ]);

        $unreadCount = Notifikasi::where('id_user', $userId)
            ->where('is_delete', 0)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
        ]);
    }

    public function send(Request $request, NotificationService $notifications)
    {
        if (!Schema::hasTable('t_notifikasi')) {
            return response()->json([
                'message' => 'Tabel notifikasi belum tersedia. Jalankan migration terlebih dahulu.',
            ], 422);
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'pesan' => ['nullable', 'string'],
            'url' => ['nullable', 'string', 'max:255'],
            'tipe' => ['nullable', 'string', 'max:50'],
            'id_user' => ['nullable', 'integer'],
            'id_users' => ['nullable', 'array'],
            'id_users.*' => ['integer'],
            'role_id' => ['nullable', 'integer'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['integer'],
            'id_perusahaan' => ['nullable', 'integer'],
            'id_kapal' => ['nullable', 'integer'],
        ]);

        $hasTarget = $request->filled('id_user')
            || !empty($request->input('id_users', []))
            || $request->filled('role_id')
            || !empty($request->input('role_ids', []));

        if (!$hasTarget) {
            return response()->json(['message' => 'Target user atau role wajib dipilih'], 422);
        }

        $result = $notifications->sendToTargets(array_merge($validated, [
            'created_by' => Session::get('userid'),
        ]));

        if ($result['count'] === 0) {
            return response()->json(['message' => 'Tidak ada user aktif sesuai target notifikasi'], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dikirim',
            'sent_count' => $result['count'],
            'target_user_ids' => $result['user_ids'],
        ]);
    }

    public function test(Request $request, NotificationService $notifications)
    {
        if (!Schema::hasTable('t_notifikasi')) {
            return response()->json([
                'message' => 'Tabel notifikasi belum tersedia. Jalankan migration terlebih dahulu.',
            ], 422);
        }

        $userId = (int) Session::get('userid');
        $hasTarget = $request->filled('id_user')
            || !empty($request->input('id_users', []))
            || $request->filled('role_id')
            || !empty($request->input('role_ids', []));

        $payload = [
            'judul' => 'Test push notification',
            'pesan' => 'Notifikasi test berhasil dikirim pada ' . date('d-m-Y H:i:s'),
            'url' => route('show'),
            'tipe' => 'test',
            'created_by' => $userId ?: null,
        ];

        if ($hasTarget) {
            $payload = array_merge($payload, $request->only([
                'id_user',
                'id_users',
                'role_id',
                'role_ids',
                'id_perusahaan',
                'id_kapal',
            ]));
        } else {
            $payload['id_user'] = $userId;
        }

        $result = $notifications->sendToTargets($payload);
        if ($result['count'] === 0) {
            return response()->json(['message' => 'Tidak ada user aktif sesuai target notifikasi'], 422);
        }

        $unreadCount = Notifikasi::where('id_user', $userId)
            ->where('is_delete', 0)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi test berhasil dikirim',
            'unread_count' => $unreadCount,
            'sent_count' => $result['count'],
            'target_user_ids' => $result['user_ids'],
            'notification' => [
                'judul' => $payload['judul'],
                'pesan' => $payload['pesan'],
                'url' => $payload['url'],
            ],
        ]);
    }
}
