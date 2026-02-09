<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Kriteria extends Model
{
    public $timestamps = false;
    protected $table = 't_kriteria_kru';
    protected $fillable = ['id', 'id_perusahaan', 'id_jabatan', 'kriteria', 'des', 'is_delete', 'created_by', 'created_date', 'changed_by', 'changed_date'];
    protected $casts = [
        'data' => 'array',
    ];
    
    public function get_perusahaan()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'id_perusahaan')->first();
    }

    public function get_jabatan()
    {
        return  $this->hasOne(Jabatan::class, 'id', 'id_jabatan')->first();
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id');
    }

}
