<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelBarang extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_kel_barang';
    protected $fillable = ['id', 'nama', 'is_delete'];

}
