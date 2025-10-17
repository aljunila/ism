<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterFile extends Model
{
    public $timestamps = false;
    protected $table = 'master_file';
    protected $fillable = ['id', 'type', 'nama', 'status'];
}
