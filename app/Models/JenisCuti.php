<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisCuti extends Model
{
    // use HasFactory;
    public $timestamps = false;
    protected $table = 'm_jenis_cuti';
    protected $fillable = ['id', 'nama', 'jumlah', 'is_delete'];
}
