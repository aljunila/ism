<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCompanyRole extends Model
{
    public $timestamps = false;
    protected $table = 'user_company_roles';
    protected $fillable = ['user_id', 'perusahaan_id', 'role_id', 'id_kapal', 'status'];
}
