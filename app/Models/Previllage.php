<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Previllage extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'previllage';
    protected $fillable = ['id', 'nama'];

}
