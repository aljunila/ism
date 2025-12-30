<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Pelabuhan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_pelabuhan';
    protected $fillable = ['id', 'id_cabang', 'nama', 'is_delete'];

    public function get_cabang()
    {
        return  $this->hasOne(Cabang::class, 'id', 'id_cabang')->first();
    }
}
