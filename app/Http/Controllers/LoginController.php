<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
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
        $password = $request->input('password');
        $data = User::where('username', $username)->first();

        try {
            if ($data && Hash::check($password, $data->password)) {
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
                        Session::put('id_perusahaan',$data->id_perusahaan);
                        Session::put('id_kapal',$data->id_kapal);
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
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Username atau password tidak sesuai']);
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

     public function password(Request $request)
    {
        $id = Session::get('userid');
        $old_password = $request->input('old_password');
        $new_password = $request->input('new_password');

        $cek = User::findOrFail($id);
        if (!Hash::check($old_password, $cek->password)) {
            return response()->json(['success' => false, 'message' => 'Password lama salah']);
        }
        $cek->password = Hash::make($new_password);
        $cek->save();
        return response()->json(['success' => true, 'message' => 'Password berhasil diubah. Silahkan login kembali dengan password baru']);
    }

    public function getMenu()
    {
        $previllage = Session::get('previllage');
        $id = Session::get('id_karyawan');

        if ($previllage == 1) {
            $menus = Menu::where('status', 'A')
                ->orderBy('no', 'ASC')
                ->get();
        } else {
            $menus = DB::table('akses')
                ->leftJoin('menu', 'menu.id', '=', 'akses.id_menu')
                ->where('menu.status', 'A')
                ->where('akses.id_karyawan', $id)
                ->where('menu_user', 'N')
                ->orderBy('no', 'ASC')
                ->select('menu.*')
                ->get();
            $dashboard = Menu::where('kode', 'dashboard')->first();
            if ($dashboard && !$menus->contains('id', $dashboard->id)) {
                $menus->prepend($dashboard);
            }
        }

        $tree = $this->buildTree($menus);

        return response()->json($tree);
    }

    private function buildTree($menus, $parentId = 0)
    {
        $branch = [];
        foreach ($menus as $menu) {
            if ($menu->id_parent == $parentId) {
                $children = $this->buildTree($menus, $menu->id);
                $menu->children = $children;
                $branch[] = $menu;
            }
        }
        return $branch;
    }

    public function buatakun() {
        return view('login.buatakun');
    }

    public function carinik(Request $request) {
        $nik = $request->input('nik');
        $get = DB::table('karyawan')
                ->leftjoin('user', 'karyawan.id', 'user.id_karyawan')
                ->leftjoin('perusahaan','perusahaan.id', 'karyawan.id_perusahaan')
                ->select('user.username', 'karyawan.nama', 'perusahaan.nama as perusahaan')
                ->where('nik', $nik)
                ->first();
        if($get) {
            return response()->json(['data' => $get]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'NIK tidak ditemukan'
            ], 404);
        }
        
    }
}
