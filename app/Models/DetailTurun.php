<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class DetailTurun extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 't_detail_turun';
    protected $fillable = ['id', 'uid', 'id_turun', 'id_barang', 'jumlah', 'kondisi', 'ket', 'is_delete', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_turun()
    {
        return  $this->hasOne(TurunBarang::class, 'id', 'id_turun')->first();
    }

    public function get_barang()
    {
        return  $this->hasOne(Barang::class, 'id', 'id_barang')->first();
    }

    public function turun()
    {
        return $this->belongsTo(TurunBarang::class, 'id_turun', 'id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id');
    }
}
