<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Previllage;
use App\Models\User;
use App\Models\Akses;
use App\Models\Menu;
use Session;
use Str;
Use DB;

class AksesController extends Controller
{
    public function show()
    {
        $data['active'] = "akses";
        return view('akses.show', $data);
    }

    public function getData()
    {
        $karyawan = DB::table('user')
                ->leftJoin('karyawan', 'user.id_karyawan', '=', 'karyawan.id')
                ->leftJoin('previllage', 'previllage.id', '=', 'user.id_previllage')
                ->select(
                    'karyawan.id',
                    'karyawan.uid',
                    'karyawan.nama',
                    'previllage.nama as previllage',
                    'user.username'
                )
                ->where('karyawan.resign', 'N')
                ->where('karyawan.status','A')
                ->get();
        
        return response()->json(['data' => $karyawan]);
    }

    public function edit($uid)
    {
        $show = DB::table('user')
                ->leftJoin('karyawan', 'user.id_karyawan', '=', 'karyawan.id')
                ->select('karyawan.id', 'user.id_previllage', 'karyawan.nama')
                ->where('uid', $uid)->first();
        $data['show'] = $show;
        $data['active'] = "akses";
        $data['previllage'] = Previllage::orderBy('id', 'DESC')->get();
        $menu = Menu::orderBy('no')->get();
        $tree = $this->buildTree($menu);
        $data['tree'] = $tree;
        $data['checkedMenuIds'] = Akses::where('id_karyawan', $show->id)
        ->pluck('id_menu')
        ->toArray();
        $data['menu'] = Menu::orderBy('id', 'ASC')->get();
        return view('akses.edit', $data);
    }

    private function buildTree($items, $parentId = 0)
    {
        $branch = [];

        foreach ($items as $item) {
            if ($item->id_parent == $parentId) {
                $children = $this->buildTree($items, $item->id);

                $branch[] = [
                    'id' => $item->id,
                    'text' => $item->nama,
                    'children' => $children
                ];
            }
        }

        return $branch;
    }

    public function saveChecked(Request $request)
    {
        Akses::where('id_karyawan', $request->input('id'))->delete();
        $checked = $request->input('checked', []);

        foreach ($checked as $menuId) {
            $cek = Menu::where('id', $menuId)->first();
            if(($cek->id_parent!=0)){
                $get = Akses::where('id_karyawan', $request->input('id'))
                        ->where('id_menu', $cek->id_parent)->get();
                if(empty($get)){
                    Akses::insert([
                        'id_karyawan' => $request->input('id'),
                        'id_menu' => $cek->id_parent,
                        ]); 
                    }
            }
            Akses::insert([
                'id_karyawan' => $request->input('id'),
                'id_menu' => $menuId,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'checked' => $checked
        ]);
    }

     public function menu()
    {
        $menus = Menu::where('status', 'A')
            ->orderBy('no', 'ASC')
            ->get();

        return response()->json($menus);
    }
}