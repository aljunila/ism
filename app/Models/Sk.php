<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sk extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'setting_keuangan';
    protected $fillable = ['id', 'id_periode', 'id_kategori', 'id_daftar', 'nama', 'nominal', 'id_sekolah', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_periode()
    {
        return  $this->hasOne(Periode::class, 'id', 'id_periode')->first();
    }

    public function get_kategori()
    {
        return  $this->hasOne(KatKeuangan::class, 'id', 'id_kategori')->first();
    }

    public function get_daftar()
    {
        return  $this->hasOne(Pendaftaran::class, 'id', 'id_daftar')->first();
    }

    public function get_sekolah()
    {
        return  $this->hasOne(Cabang::class, 'id', 'id_sekolah')->first();
    }
}
