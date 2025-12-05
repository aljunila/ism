<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MenuController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_master/menu";
        return view('acl.menu', $data);
    }

    public function data()
    {
        $query = Menu::query()->orderBy('id_parent')->orderBy('no');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('parent', function ($row) {
                return $row->id_parent == 0 ? '-' : $row->id_parent;
            })
            ->addColumn('aksi', function ($row) {
                return view('acl.partials.menu_actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'kode' => 'required|string|max:30',
            'link' => 'required|string|max:50',
            'icon' => 'nullable|string|max:50',
            'id_parent' => 'nullable|integer',
            'no' => 'required|integer',
            'menu_user' => 'required|in:Y,N',
            'status' => 'required|in:A,D',
        ]);

        Menu::create($validated);

        return response()->json(['message' => 'Menu berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'kode' => 'required|string|max:30',
            'link' => 'required|string|max:50',
            'icon' => 'nullable|string|max:50',
            'id_parent' => 'nullable|integer',
            'no' => 'required|integer',
            'menu_user' => 'required|in:Y,N',
            'status' => 'required|in:A,D',
        ]);

        $menu = Menu::findOrFail($id);
        $menu->update($validated);

        return response()->json(['message' => 'Menu berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return response()->json(['message' => 'Menu berhasil dihapus']);
    }
}
