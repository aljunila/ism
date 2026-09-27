<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelDock extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_kel_dock';
    protected $fillable = ['id', 'nama', 'is_delete'];

}
