<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class PermintaanBBM extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 't_permintaan_bbm';
    protected $fillable = ['id', 'uid', 'id_kapal', 'nomor', 'tanggal', 'jam', 'lintasan', 'jarak', 'jumlah', 'saldo_awal', 'saldo_akhir', 'ttd', 'status', 'is_delete', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    protected $casts = [
        'ttd' => 'array',
    ];

    public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }

    public function get_pelabuhan()
    {
        return  $this->hasOne(Pelabuhan::class, 'id', 'id_pelabuhan')->first();
    }
    
}
