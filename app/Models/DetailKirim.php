<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class DetailKirim extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 't_detail_kirim';
    protected $fillable = ['id', 'uid', 'id_kirim', 'id_detail_permintaan', 'jumlah', 'is_delete', 'created_by', 'created_date'];

    public function get_detail()
    {
        return  $this->hasOne(DetailPermintaan::class, 'id', 'id_detail_permintaan')->first();
    }

    public function get_kirim()
    {
        return  $this->hasOne(KirimBarang::class, 'id', 'id_kirim')->first();
    }

    public function kirim()
    {
        return $this->belongsTo(KirimBarang::class, 'id_kirim', 'id');
    }
}
