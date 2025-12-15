<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Cabang extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_cabang';
    protected $fillable = ['id', 'cabang', 'is_delete'];

}
