<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class LogGudang extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 't_log_gudang';
    protected $fillable = ['id', 'id_gudang', 'total', 'tanggal', 'keterangan', 'is_delete', 'created_by', 'created_date'];

    public function get_gudang()
    {
        return  $this->hasOne(Gudang::class, 'id', 'id_gudang')->first();
    }
}
