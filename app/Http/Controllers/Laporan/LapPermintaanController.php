<?php

namespace App\Http\Controllers\Laporan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Permintaan;
use App\Models\Barang;
use App\Models\DetailPermintaan;
use App\Models\LogBarang;
use App\Models\Kapal;
use App\Models\ChecklistData;
use App\Models\KodeForm;
use App\Models\User;
use App\Models\StatusBarang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;
use Carbon\Carbon;
use App\Exports\LapPermintaanExport;
use Maatwebsite\Excel\Facades\Excel;

class LapPermintaanController extends Controller
{
    public function laporan()
    {
        $data['active'] = "lappermintaan";
        $data['kapal'] = Kapal::where('status', 'A')->get();
        return view('laporan.permintaan.index', $data);
    }

    public function datalaporan(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $status = $request->input('status');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $query = DB::table('t_detail_permintaan as a')
                ->leftjoin('t_permintaan_barang as b', 'b.id', '=', 'a.id_permintaan')
                ->leftjoin('user as u', 'u.id', '=', 'b.created_by')
                ->select('a.*', 'b.tanggal', 'b.nomor', 'b.id_kapal', 'b.bagian', 'u.nama as peminta')
                ->where('a.is_delete', 0)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('b.id_kapal', $id_kapal);
                })
                ->when($start_date, function($query, $start_date) {
                    return $query->where('b.tanggal', '>=', $start_date);
                })
                ->when($end_date, function($query, $end_date) {
                    return $query->where('b.tanggal', '<=', $end_date);
                })
                ->orderBy('b.tanggal', 'DESC');
         if ((int) $roleJenis === 2) {
            $query->whereIn('b.id_kapal', Kapal::where('pemilik', Session::get('id_perusahaan'))->pluck('id'));
        } else if ((int) $roleJenis === 3) {
            $query->whereIn('b.id_kapal', Kapal::where('id', Session::get('id_kapal'))->pluck('id'));
        } else if ((int) $roleJenis === 6) {
            $query->whereIn('b.id_kapal', Kapal::where('id_cabang', Session::get('id_cabang'))->pluck('id'));
        }
        $this->applyPermintaanVisibility($query, 'b');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('barang', function ($row) {
                $barang = Barang::find($row->id_barang);
                return $barang ? $barang->nama : '-';
            })
            ->addColumn('satuan', function ($row) {
                $barang = Barang::find($row->id_barang);
                return $barang ? $barang->deskripsi : '-';
            })
            ->addColumn('status', function ($row) {
                return $this->normalizedStatusName($row->status);
            })
            ->make(true);
    }

    private function applyPermintaanVisibility($query, string $permintaanAlias = 'a', ?string $kapalAlias = null)
    {
        $roleJenis = $this->currentRoleJenis();

        if ($roleJenis === 2) {
            $kapalAlias = $kapalAlias ?: '__kapal_scope';
            if ($kapalAlias === '__kapal_scope') {
                $query->leftJoin('kapal as ' . $kapalAlias, $kapalAlias . '.id', '=', $permintaanAlias . '.id_kapal');
            }
            $query->where($kapalAlias . '.pemilik', Session::get('id_perusahaan'));
        } elseif ($roleJenis === 3) {
            $query->where($permintaanAlias . '.id_kapal', Session::get('id_kapal'));
        }

        return $query;
    }

    private function currentRoleJenis(): int
    {
        return (int) Session::get('previllage');
    }

     private function normalizedStatusName($statusId): string
    {
        $status = StatusBarang::find($statusId);
        if (!$status) {
            return '-';
        }

        if ((int) ($status->flag_permintaan ?? 0) === 1) {
            return 'Permintaan';
        }
        if ((int) ($status->flag_proses ?? 0) === 1 || (int) ($status->flag_berlangsung ?? 0) === 1) {
            return 'Proses (Berlangsung)';
        }

        return 'Selesai';
    }

    public function export(Request $request)
    {
        $id= $request->input('id_kapal');
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        return Excel::download(new LapPermintaanExport($id, $start, $end), 'lap_permintaan.xlsx');
    }
}
