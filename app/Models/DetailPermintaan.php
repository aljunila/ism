<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class DetailPermintaan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 't_detail_permintaan';
    protected $fillable = ['id', 'uid', 'id_permintaan', 'id_barang', 'jumlah', 'status', 'id_cabang', 'kode_po', 'is_delete', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_permintaan()
    {
        return  $this->hasOne(Permintaan::class, 'id', 'id_permintaan')->first();
    }

    public function get_barang()
    {
        return  $this->hasOne(Barang::class, 'id', 'id_barang')->first();
    }
}
