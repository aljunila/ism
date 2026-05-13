<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class TurunBarang extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 't_turun_barang';
    protected $fillable = ['id', 'uid', 'id_kapal', 'id_penerima', 'otp_code', 'otp_verified_at', 'id_cabang', 'bagian', 'nomor', 'tanggal', 'ttd', 'is_delete', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    protected $casts = [
        'ttd' => 'array',
    ];
    
    public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }

    public function get_cabang()
    {
        return  $this->hasOne(Cabang::class, 'id', 'id_cabang')->first();
    }

    public function details()
    {
        return $this->hasMany(DetailTurun::class, 'id_turun', 'id');
    }
}
