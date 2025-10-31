<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class DaftarHadir extends Model
{
    public $timestamps = false;
    protected $table = 'daftar_hadir';
    protected $fillable = ['id', 'uid', 'kode', 'id_perusahaan', 'id_kapal', 'id_notulen', 'date', 'tempat', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }

     public function get_notulen()
    {
        return  $this->hasOne(Notulen::class, 'id', 'id_notulen')->first();
    }
}
