<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class ChecklistGanti extends Model
{
    public $timestamps = false;
    protected $table = 'checklist_penggantian';
    protected $fillable = ['id', 'uid', 'kode', 'id_perusahaan', 'id_kapal', 'id_dari', 'id_kepada', 'pelabuhan', 'date', 'jam', 'note', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_dari()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_dari')->first();
    }

    public function get_kepada()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_kepada')->first();
    }

    public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }

    public function get_perusahaan()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'id_perusahaan')->first();
    }
}
