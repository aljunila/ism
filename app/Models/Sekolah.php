<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'sekolah';
    protected $fillable = ['id', 'id_yys', 'nama', 'kode', 'alamat', 'email', 'telp', 'logo', 'file', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    function logo()
    {
        if ($this->logo && file_exists(public_path('img/' . $this->logo)))
            return asset('img/' . $this->logo);
    }
}