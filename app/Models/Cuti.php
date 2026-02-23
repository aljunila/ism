<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    // use HasFactory;
    public $timestamps = false;
    protected $table = 't_cuti';
    protected $fillable = ['id', 'uid', 'id_perusahaan', 'id_karyawan', 'id_jabatan', 'id_kapal', 'data', 'id_m_cuti', 'note', 'tgl_mulai', 'tgl_selesai', 'jml_hari', 'id_pengganti', 'status', 'is_delete', 'created_by', 'created_date', 'changed_by', 'changed_date', 'approved_by', 'approved_date'];

    protected $casts = [
        'data' => 'array',
    ];

    public function get_karyawan()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_karyawan')->first();
    }

    public function get_pengganti()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_pengganti')->first();
    }

    public function get_jabatan()
    {
        return  $this->hasOne(Jabatan::class, 'id', 'id_jabatan')->first();
    }

     public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }
}
