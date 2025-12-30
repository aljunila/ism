<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class BiayaPenumpang extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_biaya_penumpang';
    protected $fillable = ['id', 'id_pelabuhan', 'kelas', 'id_kendaraan', 'nominal', 'is_delete'];

    public function get_pelabuhan()
    {
        return  $this->hasOne(Pelabuhan::class, 'id', 'id_pelabuhan')->first();
    }

    public function get_kendaraan()
    {
        return  $this->hasOne(Kendaraan::class, 'id', 'id_kendaraan')->first();
    }
}
