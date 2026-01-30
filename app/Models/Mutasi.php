<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Mutasi extends Model
{
    public $timestamps = false;
    protected $table = 't_mutasi';
    protected $fillable = ['id', 'uid', 'kode', 'dari_perusahaan', 'dari_kapal', 'id_karyawan', 'id_jabatan', 'tgl_naik', 'tgl_turun', 'ke_perusahaan', 'ke_kapal', 'ket', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_karyawan()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_karyawan')->first();
    }

    public function get_jabatan()
    {
        return  $this->hasOne(jabatan::class, 'id', 'id_jabatan')->first();
    }

    public function get_dari_perusahaan()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'dari_perusahaan')->first();
    }

    public function get_dari_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'dari_kapal')->first();
    }

    public function get_ke_perusahaan()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'ke_perusahaan')->first();
    }

    public function get_ke_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'ke_kapal')->first();
    }
}
