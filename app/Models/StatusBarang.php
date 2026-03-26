<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusBarang extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_status_barang';
    protected $fillable = ['id', 'nama', 'flag_permintaan', 'flag_proses', 'flag_berlangsung', 'is_delete'];

}
