<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class LogBarang extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 't_log_barang';
    protected $fillable = ['id', 'uid', 'id_detail_permintaan', 'status', 'event_code', 'keterangan', 'tanggal', 'img', 'is_delete', 'created_by', 'created_date'];

    public function get_detail_permintaan()
    {
        return  $this->hasOne(DetailPermintaan::class, 'id', 'id_detail_permintaan')->first();
    }

    public function detailPermintaan()
    {
        return $this->belongsTo(DetailPermintaan::class, 'id_detail_permintaan', 'id');
    }

    public function statusBarang()
    {
        return $this->belongsTo(StatusBarang::class, 'status', 'id');
    }
}
