<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Galangan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_galangan';
    protected $fillable = ['id', 'nama', 'is_delete'];
}
