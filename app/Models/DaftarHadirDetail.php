<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class DaftarHadirDetail extends Model
{
    public $timestamps = false;
    protected $table = 'daftar_hadir_detail';
    protected $fillable = ['id', 'uid', 'id_daftar_hadir', 'id_karyawan', 'id_jabatan', 'tanggal', 'status', 'created_by', 'created_date', 'changed_date'];

    public function get_karyawan()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_karyawan')->first();
    }

    public function get_jabatan()
    {
        return  $this->hasOne(Jabatan::class, 'id', 'id_jabatan')->first();
    }
}
