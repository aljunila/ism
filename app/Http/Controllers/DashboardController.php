<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perusahaan;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Kapal;
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
        if($roleJenis==1) { // superadmin
            $data['perusahaan'] = Perusahaan::count();
            $data['kapal'] = Kapal::where('status','A')->count();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign','N')->count();
            $data['user'] = DB::table('user')
                            ->leftjoin('karyawan', 'karyawan.id', 'user.id_karyawan')
                            ->where('karyawan.status','A')->where('karyawan.resign','N')
                            ->count();
        } elseif($roleJenis==2) { // admin perusahaan
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->count();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign','N')->where('id_perusahaan', $id_perusahaan)->count();
            $data['user'] = DB::table('user')
                            ->leftjoin('karyawan', 'karyawan.id', 'user.id_karyawan')
                            ->where('karyawan.status','A')->where('karyawan.resign','N')
                            ->where('karyawan.id_perusahaan', $id_perusahaan)
                            ->count();
        } elseif($roleJenis==3) { // user kapal
            $id_kapal = Session::get('id_kapal');
            $data['karyawan'] = Karyawan::where('status','A')->where('resign','N')->where('id_kapal', $id_kapal)->count();
            $data['user'] = DB::table('user')
                            ->leftjoin('karyawan', 'karyawan.id', 'user.id_karyawan')
                            ->where('karyawan.status','A')->where('karyawan.resign','N')
                            ->where('karyawan.id_kapal', $id_kapal)
                            ->count();
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
