<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\SubJobDock;
use App\Models\KelDock;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SubJobController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_master/subjob";
        $data['job'] = KelDock::where('is_delete', 0)->get();
        return view('data_master.subjob.index', $data);
    }

     public function data()
    {
        $query = SubJobDock::where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('job', function ($row) {
                $job = KelDock::find($row->id_job);
                return $job ? $job->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_master.subjob.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return SubJobDock::where('is_delete', 0)->get(['id', 'nama', 'kode']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'id_job' => 'required|integer',
        ]);
        SubJobDock::create($validated);
        return response()->json(['message' => 'subjob ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'id_job' => 'required|integer',
        ]);
        $up = SubJobDock::findOrFail($id);
        $up->update($validated);
        return response()->json(['message' => 'subjob diperbarui']);
    }

    public function destroy($id)
    {
        $up = SubJobDock::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'subjob dihapus']);
    }

     public function dataByJob(Request $request, $id)
    {
        $get = SubJobDock::where('id_job', $id)->where('is_delete', 0)->get();
        return response()->json($get);
    }
}
