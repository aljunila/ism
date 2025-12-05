<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\KodeForm;
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
            'intruksi' => 'nullable|string',
        ]);

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
        ]);

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
}
