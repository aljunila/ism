<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    public $timestamps = false;
    protected $table = 'interview';
    protected $fillable = ['id', 'uid', 'kode', 'id_perusahaan', 'nama', 'id_jabatan', 'note', 'tgl_periksa', 'id_periksa', 'id_menyetujui', 'status', 'created_by', 'created_date'];

    public function get_perusahaan()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'id_perusahaan')->first();
    }

    public function get_periksa()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_periksa')->first();
    }

    public function get_menyetujui()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_menyetujui')->first();
    }

    public function get_jabatan()
    {
        return  $this->hasOne(Jabatan::class, 'id', 'id_jabatan')->first();
    }
}
