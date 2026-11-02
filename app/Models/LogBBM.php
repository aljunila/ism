<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class LogBBM extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 't_log_bbm';
    protected $fillable = ['id', 'uid', 'id_kapal', 'saldo_awal', 'id_jenis', 'status', 'tanggal', 'me', 'ae', 'jumlah', 'saldo_akhir', 'jam', 'is_delete', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }
}
