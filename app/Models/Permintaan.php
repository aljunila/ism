<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Permintaan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 't_permintaan_barang';
    protected $fillable = ['id', 'uid', 'id_kapal', 'bagian', 'nomor', 'tanggal', 'is_delete', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }
}
