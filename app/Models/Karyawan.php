<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    // use HasFactory;
    public $timestamps = false;
    protected $table = 'karyawan';
    protected $fillable = ['id', 'uid', 'nama', 'nik', 'nip', 'telp', 'email', 'alamat', 'tgl_lahir', 'tmp_lahir', 'jk', 'status_kawin', 'status_ptkp',
                           'agama', 'gol_darah', 'pend', 'institusi_pend', 'jurusan', 'sertifikat', 'tgl_mulai', 'id_jabatan', 'id_perusahaan', 'id_kapal', 
                           'tanda_tangan', 'foto', 'status', 'resign', 'created_by', 'created_date', 'changed_by', 'changed_date', 'status_karyawan', 'npwp', 
                           'kontak_darurat', 'nama_kontak', 'telp_kontak', 'nama_bank', 'no_rekening', 'nama_rekening', 'cabang_bank', 'bpjs_kes', 'bpjs_tk'];

    public function get_jabatan()
    {
        return  $this->hasOne(Jabatan::class, 'id', 'id_jabatan')->first();
    }

    public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }

    public function get_perusahaan()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'id_perusahaan')->first();
    }

    public function getTandaTanganUrlAttribute()
    {
        if ($this->tanda_tangan && file_exists(public_path('ttd_karyawan/' . $this->tanda_tangan))) {
            return asset('ttd_karyawan/' . $this->tanda_tangan);
        }

        return null;
    }
}
