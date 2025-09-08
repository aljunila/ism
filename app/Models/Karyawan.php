<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    // use HasFactory;
    public $timestamps = false;
    protected $table = 'karyawan';
    protected $fillable = ['id', 'uid', 'nama', 'nik', 'id_jabatan', 'tanda_tangan', 'foto', 'status', 'resign',
                            'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_jabatan()
    {
        return  $this->hasOne(Jabatan::class, 'id', 'id_jabatan')->first();
    }

    public function getTandaTanganUrlAttribute()
    {
        if ($this->tanda_tangan && file_exists(public_path('ttd_karyawan/' . $this->tanda_tangan))) {
            return asset('ttd_karyawan/' . $this->tanda_tangan);
        }

        return null;
    }
}
