<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class PeriodeKondite extends Model
{
    public $timestamps = false;
    protected $table = 'periode_kondite';
    protected $fillable = ['id', 'uid', 'kode', 'id_perusahaan', 'id_kapal', 'bulan', 'tahun', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_perusahaan()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'id_perusahaan')->first();
    }

    public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }
}
