<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notulen extends Model
{
    public $timestamps = false;
    protected $table = 'notulen';
    protected $fillable = ['id', 'uid', 'tanggal', 'tempat', 'hal', 'materi', 'id_nahkoda', 'id_notulen', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

     public function get_nahkoda()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_nahkoda')->first();
    }

     public function get_notulen()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_notulen')->first();
    }
}
