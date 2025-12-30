<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Kendaraan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_kendaraan';
    protected $fillable = ['id', 'uid', 'nama', 'kode', 'is_delete'];

}
