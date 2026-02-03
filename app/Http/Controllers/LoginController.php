<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Menu;
use App\Models\Karyawan;
use App\Models\ResetPassword;
use App\Models\Role;
use App\Models\RoleMenu;
use App\Support\TokenService;
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
        $username = $request->input('username');
        $password = $request->input('password');
        $data = User::where('username', $username)->first();

        try {
            if ($data && Hash::check($password, $data->password)) {
                $roleId = $data->role_id ?? $data->id_previllage;
                $role = Role::find($roleId);
                $isSuper = $role && (int)($role->is_superadmin ?? 0) === 1;

                // Tolak login jika bukan superadmin dan jenis belum di-set
                if (!$role || (!$isSuper && is_null($role->jenis))) {
                    return response()->json(['message' => 'Role belum diatur, tidak dapat login'], 403);
                }

                if(!$isSuper) {
                    $cek = Karyawan::where('id', $data->id_karyawan)
                                    ->where('karyawan.status', 'A')
                                    ->where('karyawan.resign', 'N')
                                    ->first();
                    if(!$cek) {
                        return response()->json(['message' => 'Maaf akun Anda sudah tidak aktif'], 403);
                    }
                }

                Auth::login($data);
                $request->session()->regenerate();

                $jenis = (int) ($role->jenis ?? 0);
                $previllageLegacy = 4;
                if ($isSuper) {
                    $previllageLegacy = 1;
                } elseif ($jenis === 1) {
                    $previllageLegacy = 2;
                } elseif ($jenis === 2) {
                    $previllageLegacy = 3;
                } elseif ($jenis === 3) {
                    $previllageLegacy = 4;
                } elseif ($jenis === 4) {
                    $previllageLegacy = 5;
                }

                Session::put('userid',$data->id);
                Session::put('username',$data->username);
                Session::put('name',$data->nama);
                Session::put('userid',$data->id);
                Session::put('role_id',$roleId);
                Session::put('previllage',$previllageLegacy); // legacy mapping
                Session::put('id_karyawan',$data->id_karyawan);
                Session::put('id_perusahaan',$data->id_perusahaan);
                Session::put('id_kapal',$data->id_kapal);
                Session::put('pic',$data->pic);
                Session::put('login',TRUE);
                // set context role/perusahaan aktif
                Session::put('active_role_id', $roleId);
                Session::put('active_perusahaan_id', $data->id_perusahaan);
                // issue tokens
                $tokens = TokenService::issue($data);
                return response()->json([
                    'redirect' => '/dashboard',
                    'access_token' => $tokens['access_token'],
                    'refresh_token' => $tokens['refresh_token'],
                    'access_token_expires_at' => $tokens['access_token_expires_at'],
                    'refresh_token_expires_at' => $tokens['refresh_token_expires_at'],
                ]);
            } else { 
                return response()->json(['message' => 'Username atau password tidak sesuai'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Login gagal'], 500);
        }
    }

    public function refreshToken(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
            'username' => 'required|string',
        ]);

        $user = User::where('username', $request->input('username'))->first();
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $tokens = TokenService::refresh($user, $request->input('refresh_token'));
        if (!$tokens) {
            return response()->json(['message' => 'Refresh token invalid atau kedaluwarsa'], 401);
        }

        return response()->json([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'access_token_expires_at' => $tokens['access_token_expires_at'],
            'refresh_token_expires_at' => $tokens['refresh_token_expires_at'],
        ]);
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
        $roleId = Session::get('active_role_id', Session::get('previllage'));
        $role = Role::find($roleId);
        $isSuper = $role && (int)($role->is_superadmin ?? 0) === 1;

        if ($isSuper) {
            $menus = Menu::where('status', 'A')
                ->orderBy('no', 'ASC')
                ->get();
        } else {
            $menus = DB::table('role_menu')
                ->leftJoin('menu', 'menu.id', '=', 'role_menu.menu_id')
                ->where('role_menu.role_id', $roleId)
                ->where('menu.status', 'A')
                ->orderBy('menu.no', 'ASC')
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
                ->select('user.username', 'karyawan.id', 'karyawan.nama', 'perusahaan.nama as perusahaan')
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

    public function storeuser(Request $request) {
        $get = Karyawan::where('id', $request->input('id'))->first();
         $akun = User::create([
                'nama' => $get->nama,
                'username' => $get->nik,
                'password' => Hash::make($request->input('password1')),
                'id_previllage' => 4,
                'role_id' => 6,
                'id_perusahaan' => $get->id_perusahaan,
                'id_kapal' => $get->id_kapal,
                'id_karyawan'=> $get->id,
                'status' => 1,
                'created_by' => Session::get('userid'),
                'created_date' => date('Y-m-d H:i:s')
                ]);
    }

    public function lupapassword() {
        return view('login.lupapassword');
    }

    public function resetpassword(Request $request) {
        $nik = $request->input('nik');
        $get = DB::table('user')
                ->leftjoin('karyawan', 'karyawan.id', 'user.id_karyawan')
                ->select('user.id', 'user.username')
                ->where('nik', $nik)
                ->first();
        if($get) {
            $save = ResetPassword::create([
                'id_user'   => $get->id,
                'tgl_ajuan' => date('Y-m-d H:i:s'),
                'status'    => 'N'
            ]);
            return response()->json(['success' => true]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'NIK tidak ditemukan'
            ], 404);
        }   
    }

    public function reset($id)
    {
        $get = ResetPassword::findOrFail($id);
        $cek = User::findOrFail($get->id_user);
        $cek->password = Hash::make('123456');
        $cek->save();

        $update = ResetPassword::where('id', $id)->update([
            'tgl_reset' => date('Y-m-d H:i:s'),
            'status'    => 'R'
        ]);
    }
}
