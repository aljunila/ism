<?php

namespace App\Http\Controllers\Data_crew;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Perusahaan;
use App\Models\Jabatan;
use App\Models\KodeForm;
use App\Models\FormISM;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Session;

class KriteriaController extends Controller
{
    public function index($uid)
    {
        $data['active'] = "/form_ism";
        $get = FormISM::where('uid', $uid)->first();; 
        $roleJenis = Session::get('previllage');
        $data['jabatan'] = Jabatan::where('status', 'A')->where('kel',1)->get();
        $data['perusahaan'] = Perusahaan::find($get->id_perusahaan);
        $data['form'] = KodeForm::find($get->id_form);
        return view('data_crew.kriteria.index', $data);
    }

     public function data(Request $request)
    {
        $id_perusahaan = $request->post('id_perusahaan');
        $query = Kriteria::where('id_perusahaan', $id_perusahaan)->where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('jabatan', function ($row) {
                $jabatan = Jabatan::find($row->id_jabatan);
                return $jabatan ? $jabatan->nama : '-';
            })
            ->addColumn('perusahaan', function ($row) {
                $perusahaan = Perusahaan::find($row->id_perusahaan);
                return $perusahaan ? $perusahaan->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_crew.kriteria.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_jabatan' => 'required|integer|max:50',
            'id_perusahaan' => 'required|integer',
            'kriteria' => 'required|integer',
            'des' => 'required|string',
        ]);
        $validated['created_by'] = Session::get('userid');
        $validated['created_date'] = date('Y-m-d');
        Kriteria::create($validated);
        return response()->json(['message' => 'Biaya ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_jabatan' => 'required|integer|max:50',
            'kriteria' => 'required|integer',
            'des' => 'required|string',
        ]);
        $validated['changed_by'] = Session::get('userid');
        $up = Kriteria::findOrFail($id);
        $up->update($validated);
        return response()->json(['message' => 'Biaya diperbarui']);
    }

    public function destroy($id)
    {
        $up = Kriteria::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'Biaya dihapus']);
    }

    public function pdf(Request $request) {
        $id_perusahaan = $request->input('id_perusahaan');
        $idform = $request->input('idform');
        $perusahaan = Perusahaan::findOrFail($id_perusahaan);
        $data['get'] = Kriteria::with('jabatan')
                    ->where('id_perusahaan', $id_perusahaan)
                    ->where('is_delete', 0)
                    ->get()
                    ->groupBy('id_jabatan');
        $data['kriteriaList'] = [
            1 => 'Ijazah Pelaut',
            2 => 'Ijazah Tambahan',
            3 => 'Pengalaman (tahun)',
            4 => 'Umur (minimal)',
            5 => 'Kemampuan bahasa',
            6 => 'Lain-lain',
        ];
        $data['perusahaan'] = $perusahaan;
        $data['form'] = DB::table('kode_form as a')
                ->leftJoin('t_ism as b', function($join) use ($id_perusahaan) {
                    $join->on('a.id', '=', 'b.id_form')
                        ->where('b.id_perusahaan', $id_perusahaan)
                        ->where('b.is_delete', 0);
                })
                ->select('a.*', 'b.judul')
                ->where('a.id', $idform)->first();
        $pdf = Pdf::loadView('data_crew.kriteria.pdf', $data)
                ->setPaper('a3', 'portrait');
        return $pdf->stream($data['form']->ket.' '.$perusahaan->kode.'.pdf');
    }
}
