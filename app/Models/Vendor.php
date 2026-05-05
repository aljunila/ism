<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Vendor extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_vendor';
    protected $fillable = ['id', 'id_cabang', 'nama', 'alamat', 'telp', 'is_delete'];

    public function get_cabang()
    {
        return  $this->hasOne(Cabang::class, 'id', 'id_cabang')->first();
    }
}
