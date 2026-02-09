<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistProsedur extends Model
{
    // use HasFactory;
    public $timestamps = false;
    protected $table = 't_cuti';
    protected $fillable = ['id', 'id_karyawan', 'id_jabatan', 'id__prosedur', 'last_seen'];

    public function get_karyawan()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_karyawan')->first();
    }

    public function get_prosedur()
    {
        return  $this->hasOne(Prosedur::class, 'id', 'id_prosedur')->first();
    }

    public function get_jabatan()
    {
        return  $this->hasOne(Jabatan::class, 'id', 'id_jabatan')->first();
    }
}
