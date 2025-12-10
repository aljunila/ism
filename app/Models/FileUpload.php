<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class FileUpload extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'file_upload';
    protected $fillable = ['id', 'id_perusahaan', 'id_kapal', 'id_karyawan', 'id_file', 'tgl_terbit', 'tgl_expired', 'no', 'penerbit', 'file', 'status', 'created_by', 'changed_date'];

    public function get_perusahaan()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'id_perusahaan')->first();
    }

    public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }

    public function get_karyawan()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_karyawan')->first();
    }
}
