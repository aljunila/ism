<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubJobDock extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_subjob_dock';
    protected $fillable = ['id', 'id_job', 'nama', 'is_delete'];

}
