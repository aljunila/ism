<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Siswa extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'siswa';
    protected $fillable = ['id', 'id_sekolah', 'nama', 'panggilan', 'nik', 'nisn', 'jk', 'agama', 'tmp_lahir',
                            'tgl_lahir', 'alamat', 'email', 'telp', 'file', 'anak_ke', 'jml_sodara', 'ayah',
                            'thn_lahir_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'ibu', 'thn_lahir_ibu', 
                            'pendidikan_ibu', 'pekerjaan_ibu', 'id_daftar', 'status', 'st_pelajar', 
                            'created_date', 'changed_by', 'changed_date'];

    public function get_daftar()
    {
        return  $this->hasOne(Pendaftaran::class, 'id', 'id_daftar')->first();
    }

    public function get_sekolah()
    {
        return  $this->hasOne(Sekolah::class, 'id', 'id_sekolah')->first();
    }

    function file()
    {
        if ($this->file && file_exists(public_path('file_student/' . $this->file)))
            return asset('file_student/' . $this->file);
    }
}
