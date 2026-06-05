<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perusahaan;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Role;
use App\Models\Kapal;
use App\Models\FileUpload;
use Illuminate\Support\Facades\Schema;
use Alert;
use Session;
Use Carbon\Carbon;
Use DB;

class DashboardController extends Controller
{
    private function normalizeStatusStage($nama, $flagPermintaan = 0, $flagProses = 0, $flagBerlangsung = 0)
    {
        if ((int) $flagPermintaan === 1) {
            return 'Permintaan';
        }

        if ((int) $flagProses === 1 || (int) $flagBerlangsung === 1) {
            return 'Proses (Berlangsung)';
        }

        $lower = strtolower((string) $nama);
        if (str_contains($lower, 'permintaan')) {
            return 'Permintaan';
        }
        if (str_contains($lower, 'proses') || str_contains($lower, 'berlangsung')) {
            return 'Proses (Berlangsung)';
        }
        if (str_contains($lower, 'selesai') || str_contains($lower, 'done') || str_contains($lower, 'complete')) {
            return 'Selesai';
        }

        return 'Selesai';
    }

    public function show()
    {
        $data['active'] = "dashboard";
        $roleJenis = Session::get('previllage'); // sudah dimapping di middleware
        $userId = Session::get('userid');
        $data['pre'] = $roleJenis;
        $id_perusahaan = Session::get('id_perusahaan');
        $data['com'] = Perusahaan::where('id', $id_perusahaan)->first();
        $data['document'] = collect();
        $data['doc_kru'] = collect();
        $data['count_doc'] = 0;
        $data['count_dockru'] = 0;
        $data['notification_users'] = collect();
        $data['notification_roles'] = collect();
        $data['pending_kirim_otps'] = collect();
        $data['pending_turun_otps'] = collect();
        $tanggal = Carbon::today()->addDays(45)->format('Y-m-d');
        if(($roleJenis==1) || ($roleJenis==5)) { // superadmin
            $data['perusahaan'] = Perusahaan::count();
            $data['kapal'] = Kapal::where('status','A')->count();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign','N')->count();
            $data['user'] = DB::table('user')
                            ->leftjoin('karyawan', 'karyawan.id', 'user.id_karyawan')
                            ->where('karyawan.status','A')->where('karyawan.resign','N')
                            ->count();
            $data['document'] = FileUpload::where('status', 'A')->where('tgl_expired', '<=', $tanggal)->whereNotNull('id_kapal')->get();
            $data['count_doc'] = count($data['document']);
            $data['doc_kru'] =  DB::table('file_upload as a')
                                ->select('a.*', 'b.nama as karyawan', 'c.nama as kapal', 'd.nama as filename')
                                ->leftJoin('karyawan as b', 'a.id_karyawan', '=', 'b.id')
                                ->leftJoin('kapal as c', 'b.id_kapal', '=', 'c.id')
                                ->leftJoin('master_file as d', 'a.id_file', '=', 'd.id')
                                ->where('a.status', 'A')
                                ->whereDate('a.tgl_expired', '<=', '2026-06-26')
                                ->whereNotNull('a.id_karyawan')
                                ->get();
            $data['count_dockru'] = count($data['doc_kru']);
        } elseif($roleJenis==2) { // admin perusahaan
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->count();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign','N')->where('id_perusahaan', $id_perusahaan)->count();
            $data['user'] = DB::table('user')
                            ->leftjoin('karyawan', 'karyawan.id', 'user.id_karyawan')
                            ->where('karyawan.status','A')->where('karyawan.resign','N')
                            ->where('karyawan.id_perusahaan', $id_perusahaan)
                            ->count();
            $data['document'] = DB::table('file_upload as a')
                                ->leftJoin('kapal as b', 'a.id_kapal', '=', 'b.id')
                                ->select('a.*')
                                ->where('a.status', 'A')->where('a.tgl_expired', '<=', $tanggal)->where('b.pemilik', $id_perusahaan)->get();
            $data['count_doc'] = count($data['document']);
            $data['doc_kru'] = DB::table('file_upload as a')
                                ->select('a.*', 'b.nama as karyawan', 'c.nama as kapal', 'd.nama as filename')
                                ->leftJoin('karyawan as b', 'a.id_karyawan', '=', 'b.id')
                                ->leftJoin('kapal as c', 'b.id_kapal', '=', 'c.id')
                                ->leftJoin('master_file as d', 'a.id_file', '=', 'd.id')
                                ->where('a.status', 'A')
                                ->whereDate('a.tgl_expired', '<=', '2026-06-26')
                                ->where('b.id_perusahaan', $id_perusahaan)
                                ->whereNotNull('a.id_karyawan')
                                ->get();
            $data['count_dockru'] = count($data['doc_kru']);
        } elseif($roleJenis==3) { // user kapal
            $id_kapal = Session::get('id_kapal');
            $kapal = Kapal::where('id', $id_kapal)->first();
            if (!$data['com'] && $kapal) {
                $data['com'] = Perusahaan::where('id', $kapal->pemilik)->first();
            }
            $data['karyawan'] = Karyawan::where('status','A')->where('resign','N')->where('id_kapal', $id_kapal)->count();
            $data['user'] = DB::table('user')
                            ->leftjoin('karyawan', 'karyawan.id', 'user.id_karyawan')
                            ->where('karyawan.status','A')->where('karyawan.resign','N')
                            ->where('karyawan.id_kapal', $id_kapal)
                            ->count();
            $data['document'] = FileUpload::where('status', 'A')
                                ->where('tgl_expired', '<=', $tanggal)
                                ->where('id_kapal', $id_kapal)
                                ->get();
            $data['count_doc'] = count($data['document']);
            $data['doc_kru'] =  DB::table('file_upload as a')
                                ->select('a.*', 'b.nama as karyawan', 'c.nama as kapal', 'd.nama as filename')
                                ->leftJoin('karyawan as b', 'a.id_karyawan', '=', 'b.id')
                                ->leftJoin('kapal as c', 'b.id_kapal', '=', 'c.id')
                                ->leftJoin('master_file as d', 'a.id_file', '=', 'd.id')
                                ->where('a.status', 'A')
                                ->whereDate('a.tgl_expired', '<=', '2026-06-26')
                                ->where('b.id_kapal', $id_kapal)
                                ->whereNotNull('a.id_karyawan')
                                ->get();
            $data['count_dockru'] = count($data['doc_kru']);
        } elseif($roleJenis==6) { // user kapal
            $id_cabang = Session::get('id_cabang');
            $kapal = Kapal::where('id_cabang', $id_cabang)->get();
            $data['karyawan'] = DB::table('karyawan as a')
                                ->leftJoin('kapal as b', 'a.id_kapal', '=', 'b.id')
                                ->select('a.*')
                                ->where('a.status','A')->where('a.resign','N')
                                ->where(function ($q) use ($id_cabang) {
                                    $q->where('a.id_cabang', $id_cabang)->orWhere('b.id_cabang', $id_cabang);
                                })->count();
            $data['user'] = DB::table('user')
                            ->leftjoin('karyawan', 'karyawan.id', 'user.id_karyawan')
                            ->leftJoin('kapal', 'karyawan.id_kapal', '=', 'kapal.id')
                            ->where('karyawan.status','A')->where('karyawan.resign','N')
                            ->where(function ($q) use ($id_cabang) {
                                    $q->where('karyawan.id_cabang', $id_cabang)->orWhere('kapal.id_cabang', $id_cabang);
                                })
                            ->count();
            $query = FileUpload::where('status', 'A')->where('tgl_expired', '<=', $tanggal);
                    $query->whereIn('id_kapal', Kapal::where('id_cabang', Session::get('id_cabang'))->pluck('id'));
            $data['document'] = $query->get();
            $data['count_doc'] = $query->count();
            $data['doc_kru'] =  DB::table('file_upload as a')
                                ->select('a.*', 'b.nama as karyawan', 'c.nama as kapal', 'd.nama as filename')
                                ->leftJoin('karyawan as b', 'a.id_karyawan', '=', 'b.id')
                                ->leftJoin('kapal as c', 'b.id_kapal', '=', 'c.id')
                                ->leftJoin('master_file as d', 'a.id_file', '=', 'd.id')
                                ->where('a.status', 'A')
                                ->whereDate('a.tgl_expired', '<=', '2026-06-26')
                                ->where('c.id_cabang', $id_cabang)
                                ->whereNotNull('a.id_karyawan')
                                ->get();
            $data['count_dockru'] = count($data['doc_kru']);
        } else {
            $id_user = Session::get('userid');

            $data['prosedur'] = DB::table('prosedur as a')
                ->leftJoin('view_prosedur as b', function($join) use ($id_user) {
                    $join->on('a.id', '=', 'b.id_prosedur')
                        ->where('b.id_user', '=', $id_user);
                })
                ->select(
                    'a.kode',
                    'b.jml_lihat',
                    'b.jml_download',
                    'b.update_lihat',
                    'b.update_download'
                )
                ->where('a.status', 'A')
                ->where('a.id_perusahaan', $id_perusahaan)
                ->orderBy('a.id')
                ->get();
        }
        $permintaanQuery = DB::table('t_permintaan_barang as a')
            ->leftJoin('kapal as b', 'b.id', '=', 'a.id_kapal')
            ->leftJoin('user as c', 'c.id', '=', 'a.created_by')
            ->select(
                'a.id',
                'a.nomor',
                'a.tanggal',
                'a.bagian',
                'a.id_kapal',
                'b.nama as kapal',
                'c.nama as peminta'
            )
            ->where('a.is_delete', 0);

        if ((int) $roleJenis !== 1) {
            $permintaanQuery->where('a.created_by', $userId);
        }

        $data['permintaan_dashboard'] = $permintaanQuery
            ->orderBy('a.tanggal', 'DESC')
            ->orderBy('a.id', 'DESC')
            ->paginate(10, ['*'], 'permintaan_page');

        if (Schema::hasTable('user')) {
            $notificationUsers = User::query()
                ->select('id', 'nama', 'username', 'id_perusahaan', 'id_kapal')
                ->when(Schema::hasColumn('user', 'is_delete'), function ($query) {
                    $query->where('is_delete', 0);
                })
                ->when(Schema::hasColumn('user', 'status'), function ($query) {
                    $query->where(function ($q) {
                        $q->where('status', 1)->orWhere('status', 'A');
                    });
                });

            if ((int) $roleJenis === 2) {
                $notificationUsers->where('id_perusahaan', $id_perusahaan);
            } elseif ((int) $roleJenis === 3) {
                $notificationUsers->where('id_kapal', Session::get('id_kapal'));
            } elseif ((int) $roleJenis === 4) {
                $notificationUsers->where('id', $userId);
            }

            $data['notification_users'] = $notificationUsers
                ->orderBy('nama')
                ->limit(200)
                ->get();
        }

        if (Schema::hasTable('roles')) {
            $notificationRoles = Role::query()
                ->select('id', 'nama')
                ->where('status', 'A');

            if ((int) $roleJenis === 2 && Schema::hasColumn('roles', 'is_superadmin')) {
                $notificationRoles->where('is_superadmin', 0);
            } elseif ((int) $roleJenis === 4) {
                $notificationRoles->whereRaw('1 = 0');
            }

            $data['notification_roles'] = $notificationRoles
                ->orderBy('nama')
                ->get();
        }

        if (Schema::hasTable('t_kirim_otp')) {
            $data['pending_kirim_otps'] = DB::table('t_kirim_otp as o')
                ->leftJoin('user as u', 'u.id', '=', 'o.created_by')
                ->select(
                    'o.id',
                    'o.otp_code',
                    'o.expires_at',
                    'o.created_date',
                    'u.nama as pengirim_nama',
                    'u.username as pengirim_username'
                )
                ->where('o.id_penerima', $userId)
                ->whereNull('o.used_at')
                ->where('o.is_delete', 0)
                ->where('o.expires_at', '>=', Carbon::now())
                ->orderByDesc('o.created_date')
                ->limit(5)
                ->get();
        }

        if (Schema::hasTable('t_turun_otp')) {
            $data['pending_turun_otps'] = DB::table('t_turun_otp as o')
                ->leftJoin('user as u', 'u.id', '=', 'o.created_by')
                ->select(
                    'o.id',
                    'o.otp_code',
                    'o.expires_at',
                    'o.created_date',
                    'u.nama as pengirim_nama',
                    'u.username as pengirim_username'
                )
                ->where('o.id_penerima', $userId)
                ->whereNull('o.used_at')
                ->where('o.is_delete', 0)
                ->where('o.expires_at', '>=', Carbon::now())
                ->orderByDesc('o.created_date')
                ->limit(5)
                ->get();
        }

        return view('dashboard.show', $data);
    }

    public function permintaanDetail($id)
    {
        $roleJenis = Session::get('previllage');
        $userId = Session::get('userid');

        $header = DB::table('t_permintaan_barang as a')
            ->leftJoin('kapal as b', 'b.id', '=', 'a.id_kapal')
            ->leftJoin('user as c', 'c.id', '=', 'a.created_by')
            ->select(
                'a.id',
                'a.nomor',
                'a.tanggal',
                'a.bagian',
                'a.created_by',
                'b.nama as kapal',
                'c.nama as peminta'
            )
            ->where('a.id', $id)
            ->where('a.is_delete', 0);

        if ((int) $roleJenis !== 1) {
            $header->where('a.created_by', $userId);
        }

        $header = $header
            ->first();

        if (!$header) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $items = DB::table('t_detail_permintaan as a')
            ->leftJoin('m_barang as b', 'b.id', '=', 'a.id_barang')
            ->leftJoin('m_status_barang as c', 'c.id', '=', 'a.status')
            ->select(
                'a.id',
                'a.jumlah',
                'b.nama as barang',
                'b.deskripsi as satuan',
                'c.nama as status',
                DB::raw('COALESCE(c.flag_permintaan, 0) as flag_permintaan'),
                DB::raw('COALESCE(c.flag_proses, 0) as flag_proses'),
                DB::raw('COALESCE(c.flag_berlangsung, 0) as flag_berlangsung')
            )
            ->where('a.id_permintaan', $id)
            ->where('a.is_delete', 0)
            ->orderBy('a.id', 'ASC')
            ->get();

        $items = $items->map(function ($item) {
            $item->status = $this->normalizeStatusStage(
                $item->status,
                $item->flag_permintaan ?? 0,
                $item->flag_proses ?? 0,
                $item->flag_berlangsung ?? 0
            );
            return $item;
        });

        return response()->json([
            'header' => $header,
            'items' => $items
        ]);
    }

    public function permintaanLog($idDetail)
    {
        $roleJenis = Session::get('previllage');
        $userId = Session::get('userid');

        $rows = DB::table('t_log_barang as a')
            ->leftJoin('t_detail_permintaan as d', 'd.id', '=', 'a.id_detail_permintaan')
            ->leftJoin('t_permintaan_barang as p', 'p.id', '=', 'd.id_permintaan')
            ->leftJoin('m_status_barang as b', 'b.id', '=', 'a.status')
            ->leftJoin('user as c', 'c.id', '=', 'a.created_by')
            ->select(
                'a.id',
                'a.tanggal',
                'a.keterangan',
                'b.nama as status',
                'c.nama as created',
                DB::raw('COALESCE(b.flag_permintaan, 0) as flag_permintaan'),
                DB::raw('COALESCE(b.flag_proses, 0) as flag_proses'),
                DB::raw('COALESCE(b.flag_berlangsung, 0) as flag_berlangsung')
            )
            ->where('a.id_detail_permintaan', $idDetail)
            ->where('a.is_delete', 0);

        if ((int) $roleJenis !== 1) {
            $rows->where('p.created_by', $userId);
        }

        $rows = $rows
            ->orderBy('a.created_date', 'DESC')
            ->orderBy('a.id', 'DESC')
            ->get();

        $rows = $rows->map(function ($row) {
            $row->status = $this->normalizeStatusStage(
                $row->status,
                $row->flag_permintaan ?? 0,
                $row->flag_proses ?? 0,
                $row->flag_berlangsung ?? 0
            );
            return $row;
        });

        return response()->json($rows);
    }
}
