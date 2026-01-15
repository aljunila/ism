<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class ChecklistData extends Model
{
    public $timestamps = false;
    protected $table = 'checklist_data';
    protected $fillable = ['id', 'uid', 'id_form', 'kode', 'id_perusahaan', 'id_karyawan', 'id_jabatan', 'id_kapal', 'date', 'time', 'data', 'keterangan', 'id_karyawan2', 'id_jabatan2', 'note', 'pj', 'id_mengetahui', 'id_mentor', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    protected $casts = [
        'data' => 'array',
        'keterangan'  => 'array',
        'pj'   => 'array',
    ];

    public function get_karyawan()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_karyawan')->first();
    }

    public function get_karyawan2()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_karyawan2')->first();
    }

    public function get_jabatan()
    {
        return  $this->hasOne(Jabatan::class, 'id', 'id_jabatan')->first();
    }

    public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }

    public function get_mengetahui()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_mengetahui')->first();
    }

    public function get_mentor()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_mentor')->first();
    }

    public function get_perusahaan()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'id_perusahaan')->first();
    }

    public function get_form()
    {
        return  $this->hasOne(KodeForm::class, 'id', 'id_form')->first();
    }
}
