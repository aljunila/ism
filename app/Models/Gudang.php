<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Gudang extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 't_gudang';
    protected $fillable = ['id', 'uid', 'id_cabang', 'id_kapal', 'id_barang', 'jumlah', 'baik', 'habis', 'keterangan', 'is_delete', 'changed_date'];

    public function get_cabang()
    {
        return  $this->hasOne(Cabang::class, 'id', 'id_cabang')->first();
    }

    public function get_barang()
    {
        return  $this->hasOne(Barang::class, 'id', 'id_barang')->first();
    }

    public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }
}
