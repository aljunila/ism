<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_divisi';
    protected $fillable = ['id', 'nama', 'is_delete'];

}
