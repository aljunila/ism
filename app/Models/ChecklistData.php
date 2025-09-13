<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class ChecklistData extends Model
{
    public $timestamps = false;
    protected $table = 'checklist_data';
    protected $fillable = ['id', 'uid', 'kode', 'id_karyawan', 'id_jabatan', 'id_kapal', 'date', 'note', 'id_mengetahui', 'id_mentor', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_karyawan()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_karyawan')->first();
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
}
