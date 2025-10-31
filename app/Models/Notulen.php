<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notulen extends Model
{
    public $timestamps = false;
    protected $table = 'notulen';
    protected $fillable = ['id', 'uid', 'kode', 'id_perusahaan', 'id_kapal', 'tanggal', 'tempat', 'hal', 'materi', 'id_nahkoda', 'id_notulen', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

     public function get_nahkoda()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_nahkoda')->first();
    }

    public function get_notulen()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_notulen')->first();
    }

    public function get_perusahaan()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'id_perusahaan')->first();
    }

    public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }

    public function get_kode()
    {
        return  $this->hasOne(KodeForm::class, 'kode', 'kode')->first();
    }
}
