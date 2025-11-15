<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public $timestamps = false;
    protected $table = 'review';
    protected $fillable = ['id', 'uid', 'kode', 'id_perusahaan', 'id_kapal', 'no_review', 'tgl_review', 'tgl_diterima', 'hasil', 'ket', 'id_nahkoda', 'id_dpa', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }

    public function get_perusahaan()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'id_perusahaan')->first();
    }

    public function get_nahkoda()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_nahkoda')->first();
    }

    public function get_dpa()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_dpa')->first();
    }
}
