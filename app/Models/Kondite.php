<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Kondite extends Model
{
    public $timestamps = false;
    protected $table = 't_kondite';
    protected $fillable = ['id', 'uid', 'id_periode', 'id_karyawan', 'id_jabatan', 'tgl_nilai', 'data', 'rekomendasi', 'note', 'id_penilai_1', 'id_penilai_2', 'id_mengetahui', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];
    protected $casts = [
        'data' => 'array',
    ];
    
    public function get_karyawan()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_karyawan')->first();
    }

    public function get_jabatan()
    {
        return  $this->hasOne(Jabatan::class, 'id', 'id_jabatan')->first();
    }

    public function get_periode()
    {
        return  $this->hasOne(PeriodeKondite::class, 'id', 'id_periode')->first();
    }

    public function get_penilai1()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_penilai_1')->first();
    }

    public function get_penilai2()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_penilai_2')->first();
    }

    public function get_mengetahui()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_mengetahui')->first();
    }
}
