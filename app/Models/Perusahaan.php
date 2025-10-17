<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    // use HasFactory;
    public $timestamps = false;
    protected $table = 'perusahaan';
    protected $fillable = ['id', 'uid', 'nama', 'kode', 'alamat', 'email', 'telp', 'direktur', 'npwp', 'nib', 'logo'];

    public function getLogoUrlAttribute()
    {
        if ($this->logo && file_exists(public_path('img/' . $this->logo))) {
            return asset('img/' . $this->logo);
        }

        return null;
    }
}
