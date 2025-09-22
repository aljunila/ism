<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class GantiKKM extends Model
{
    public $timestamps = false;
    protected $table = 'ganti_kkm';
    protected $fillable = ['id', 'uid', 'nomer','id_kepada', 'id_perusahaan', 'tanggal', 'jam', 'id_lama', 'fo', 'do', 'fw', 'id_baru', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_lama()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_lama')->first();
    }

    public function get_baru()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_baru')->first();
    }

    public function get_kepada()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_kepada')->first();
    }

    public function get_perusahaan()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'id_perusahaan')->first();
    }
}
