<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\KodeForm;
use App\Models\FormISM;
use App\Models\Perusahaan;
use App\Models\Menu;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KodeFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['active'] = "/data_master/kode_form";
        $data['perusahaan'] = Perusahaan::where('status', 'A')->get();
        $data['menu'] = Menu::where('status', 'A')->where('menu_user', 'N')->get();
        return view('data_master.kode_form.index', $data);
    }

    public function data()
    {
        $query = KodeForm::where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return view('data_master.kode_form.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20',
            'nama' => 'required|string|max:100',
            'ket' => 'nullable|string|max:50',
            'ket' => 'nullable|string|max:50',
            'pj' => 'nullable|string|max:20',
            'kode_file' => 'nullable|string|max:20',
            'periode' => 'nullable|string|max:20',
            'id_menu' => 'nullable|integer|max:100',
            'bagian' => 'nullable|string|max:20',
            'kel' => 'nullable|string|max:20',
            'intruksi' => 'nullable|string',
        ]);
        if($request->input('id_menu')){
            $menu = Menu::find($request->input('id_menu'));
            $validated['link'] = $menu->link;
        }
        $exists = KodeForm::where('is_delete', 0)
            ->where(function ($q) use ($validated) {
                $q->where('kode', $validated['kode']);
                if (!empty($validated['ket'])) {
                    $q->orWhere('ket', $validated['ket']);
                }
            })->exists();

        if ($exists) {
            return response()->json(['message' => 'Ada data yang sama pada database untuk kode '.$validated['kode']], 422);
        }

        KodeForm::create($validated);

        return response()->json(['message' => 'Berhasil menambah kode form']);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20',
            'nama' => 'required|string|max:100',
            'ket' => 'nullable|string|max:50',
            'intruksi' => 'nullable|string',
            'pj' => 'nullable|string|max:20',
            'kode_file' => 'nullable|string|max:20',
            'periode' => 'nullable|string|max:20',
            'id_menu' => 'nullable|string|max:100',
            'bagian' => 'nullable|string|max:20',
            'kel' => 'nullable|string|max:20',
        ]);
        if($request->input('id_menu')){
            $menu = Menu::find($request->input('id_menu'));
            $validated['link'] = $menu->link;
        }

        $kodeForm = KodeForm::findOrFail($id);

        $exists = KodeForm::where('is_delete', 0)
            ->where('id', '!=', $id)
            ->where(function ($q) use ($validated) {
                $q->where('kode', $validated['kode']);
                if (!empty($validated['ket'])) {
                    $q->orWhere('ket', $validated['ket']);
                }
            })->exists();

        if ($exists) {
            return response()->json(['message' => 'Ada data yang sama pada database untuk kode '.$validated['kode']], 422);
        }

        $kodeForm->update($validated);

        return response()->json(['message' => 'Berhasil memperbarui kode form']);
    }

    public function destroy(string $id)
    {
        $kodeForm = KodeForm::findOrFail($id);
        $kodeForm->update(['is_delete' => 1]);

        return response()->json(['message' => 'Berhasil menghapus kode form']);
    }

    
    public function form()
    {
        $data['active'] = "/form_ism";
        return view('data_master.kode_form.form', $data);
    }

    public function ism()
    {
        $id_perusahaan = Session::get('id_perusahaan');
        $roleJenis = Session::get('previllage');

        $query = FormISM::where('is_delete', 0)
                ->when((($roleJenis == 1) or ($roleJenis == 5)), function ($q) { return $q; })
                ->when($roleJenis == 2 && $id_perusahaan, function ($q) use ($id_perusahaan) {
                    return $q->where('id_perusahaan', $id_perusahaan);
                })
                ->orderBy('id_perusahaan', 'asc')->orderBy('judul', 'asc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama', function ($row) {
                $form = KodeForm::find($row->id_form);
                return $form ? $form->nama : '-';
            })
            ->addColumn('perusahaan', function ($row) {
                $perusahaan = Perusahaan::find($row->id_perusahaan);
                return $perusahaan ? $perusahaan->nama : '-';
            })
             ->addColumn('link', function ($row) {
                $form = KodeForm::find($row->id_form);
                return $form ? $form->link : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_master.kode_form.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
