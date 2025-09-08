<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Menu;
use App\Models\Karyawan;
use Session;
use Validator;
use DB;

class LoginController extends Controller
{
    public function login()
    {
        return view('login.show');
    }

    public function actionlogin(Request $request)
    {
        $rules = [
            'username'              => 'required|string',
            'password'              => 'required|string'
        ];
        $messages = [
            'username.required'     => 'Username wajib diisi',
            'username.username'     => 'Username tidak valid',
            'password.required'     => 'Password wajib diisi',
            'password.string'       => 'Password harus berupa string'
        ];

        $username = $request->input('username');
        $password = sha1($request->input('password'));
        $data = User::where('username',$username)->where('password', $password)->first();
        if($data){ 
                if($data->id_previllage!=1) {
                    $cek = Karyawan::where('id', $data->id_karyawan)
                                    ->where('karyawan.status', 'A')
                                    ->where('karyawan.resign', 'N')
                                    ->first();
                    if($cek) {  
                        Session::put('userid',$data->id);
                        Session::put('username',$data->username);
                        Session::put('name',$data->nama);
                        Session::put('userid',$data->id);
                        Session::put('previllage',$data->id_previllage);
                        Session::put('id_karyawan',$data->id_karyawan);
                        Session::put('pic',$data->pic);
                        Session::put('login',TRUE);
                        return redirect('/dashboard');
                    } else {
                        $messages = ['Maaf akun Anda sudah tidak aktif'];
                        return redirect()->back()->withErrors($messages);
                    }
                } else {
                    Session::put('userid',$data->id);
                    Session::put('username',$data->username);
                    Session::put('name',$data->nama);
                    Session::put('userid',$data->id);
                    Session::put('previllage',$data->id_previllage);
                    Session::put('id_karyawan',$data->id_karyawan);
                    Session::put('pic',$data->pic);
                    Session::put('login',TRUE);
                    return redirect('/dashboard');
                }
        } else { 
                $messages = ['Username atau password tidak sesuai'];
                return redirect()->back()->withErrors($messages);
            }
    }

    public function logout(){
        Session::flush();
        return redirect('/');
    }

    public function getParents()
    {
        $menus = Menu::where('status', 'A')
            ->where('id_parent', 0)
            ->orderBy('no', 'ASC')
            ->get()
            ->map(function ($menu) {
                return [
                    'id'        => $menu->id,
                    'nama'      => $menu->nama,
                    'kode'      => $menu->kode,
                    'link'      => $menu->link,
                    'icon'      => $menu->icon,
                    'has_child' => Menu::where('id_parent', $menu->id)->exists(),
                ];
            });

        return response()->json($menus);
    }

    public function getChildren($parentId)
    {
        $menus = Menu::where('status', 'A')
            ->where('id_parent', $parentId)
            ->orderBy('no', 'ASC')
            ->get()
            ->map(function ($menu) {
                return [
                    'id'        => $menu->id,
                    'nama'      => $menu->nama,
                    'kode'      => $menu->kode,
                    'link'      => $menu->link,
                    'icon'      => $menu->icon,
                    'has_child' => Menu::where('id_parent', $menu->id)->exists(),
                ];
            });

        return response()->json($menus);
    }
}
