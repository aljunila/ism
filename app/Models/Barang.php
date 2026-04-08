<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Barang extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_barang';
    protected $fillable = ['id', 'id_kel_barang', 'kode', 'nama', 'deskripsi', 'is_delete'];

    public function get_kel_barang()
    {
        return  $this->hasOne(KelBarang::class, 'id', 'id_kel_barang')->first();
    }
}
