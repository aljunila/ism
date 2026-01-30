<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Pelatihan extends Model
{
    public $timestamps = false;
    protected $table = 't_pelatihan';
    protected $fillable = ['id', 'uid', 'kode', 'id_perusahaan', 'id_kapal', 'id_karyawan', 'id_jabatan', 'nama', 'tgl_mulai', 'tgl_selesai', 'tempat', 'hasil', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_karyawan()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_karyawan')->first();
    }

    public function get_perusahaan()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'id_perusahaan')->first();
    }

    public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }

    public function get_jabatan()
    {
        return  $this->hasOne(Jabatan::class, 'id', 'id_jabatan')->first();
    }

}
