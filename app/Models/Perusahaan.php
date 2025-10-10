<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    // use HasFactory;
    public $timestamps = false;
    protected $table = 'perusahaan';
    protected $fillable = ['id', 'uid', 'nama', 'alamat', 'email', 'telp', 'direktur', 'npwp', 'nib', 'logo'];
}
