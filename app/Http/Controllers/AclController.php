<?php

namespace App\Http\Controllers;

class AclController extends Controller
{
    public function menu()
    {
        $data['active'] = '/data_master/menu';
        return view('acl.menu', $data);
    }

    public function roles()
    {
        $data['active'] = '/acl/roles';
        return view('acl.roles', $data);
    }

    public function users()
    {
        $data['active'] = '/acl/users';
        return view('acl.users', $data);
    }
}
