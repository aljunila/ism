<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Akses extends Model
{
    public $timestamps = false;
    protected $table = 'akses';
    protected $fillable = ['id', 'id_karyawan', 'id_menu'];

    public function get_karyawan()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_karyawan')->first();
    }

    public function get_menu()
    {
        return  $this->hasOne(Menu::class, 'id', 'id_menu')->first();
    }
}
