<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoBarang extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 't_po_barang';
    protected $fillable = [
        'id',
        'uid',
        'id_detail_permintaan',
        'nomor_po',
        'status_po',
        'jumlah',
        'amount',
        'id_currency',
        'rate_to_idr',
        'amount_idr',
        'tanggal_po',
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
