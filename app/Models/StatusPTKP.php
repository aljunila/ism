<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class StatusPTKP extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'status_ptkp';
    protected $fillable = ['id', 'kode', 'nama', 'tarif'];

}
