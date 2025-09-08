<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Akun extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'akun';
    protected $fillable = ['id', 'name', 'username', 'password'];

}
