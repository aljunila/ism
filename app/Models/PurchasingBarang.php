<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasingBarang extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 't_purchasing_barang';
    protected $fillable = [
        'id',
        'uid',
        'id_detail_permintaan',
        'vendor',
        'status_purchasing',
        'amount',
        'id_currency',
        'rate_to_idr',
        'amount_idr',
        'tanggal_beli',
        'tanggal_kirim',
        'shipping_mode',
        'shipping_point',
        'keterangan',
        'is_delete',
        'created_by',
        'created_date',
        'changed_by',
        'changed_date',
    ];

    public function detailPermintaan()
    {
        return $this->belongsTo(DetailPermintaan::class, 'id_detail_permintaan', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'id_currency', 'id');
    }
}
