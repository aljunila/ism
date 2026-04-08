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
    protected $fillable = ['id', 'uid', 'id_permintaan', 'id_barang', 'jumlah', 'status', 'id_cabang', 'kode_po', 'procurement_channel', 'flow_stage', 'is_delete', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_permintaan()
    {
        return  $this->hasOne(Permintaan::class, 'id', 'id_permintaan')->first();
    }

    public function get_barang()
    {
        return  $this->hasOne(Barang::class, 'id', 'id_barang')->first();
    }

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'id_permintaan', 'id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id');
    }

    public function logs()
    {
        return $this->hasMany(LogBarang::class, 'id_detail_permintaan', 'id');
    }

    public function poRecords()
    {
        return $this->hasMany(PoBarang::class, 'id_detail_permintaan', 'id');
    }

    public function purchasingRecords()
    {
        return $this->hasMany(PurchasingBarang::class, 'id_detail_permintaan', 'id');
    }
}
