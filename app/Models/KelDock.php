<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelDock extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_kel_dock';
    protected $fillable = ['id', 'id_gal', 'nama', 'is_delete'];

    public function get_gal()
    {
        return  $this->hasOne(Galangan::class, 'id', 'gal')->first();
    }
}
