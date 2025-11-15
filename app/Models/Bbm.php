<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Bbm extends Model
{
    public $timestamps = false;
    protected $table = 'bbm';
    protected $fillable = ['id', 'uid', 'kode', 'id_perusahaan', 'id_kapal', 'tanggal', 'waktu', 'no_pelayaran', 'pelabuhan', 'fo', 'mdo', 'ket', 'id_nahkoda', 'id_kkm', 'id_jaga', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_nahkoda()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_nahkoda')->first();
    }

    public function get_kkm()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_kkm')->first();
    }

    public function get_jaga()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_jaga')->first();
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
