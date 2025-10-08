<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Prosedur extends Model
{
    public $timestamps = false;
    protected $table = 'prosedur';
    protected $fillable = ['id', 'uid', 'id_perusahaan', 'kode', 'judul', 'no_dokumen', 'edisi', 'tgl_terbit', 'status_manual', 'cover', 'isi',
                        'prepered_by', 'enforced_by', 'file', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_prepered()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'prepered_by')->first();
    }

    public function get_enforced()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'enforced_by')->first();
    }

    public function get_perusahaan()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'id_perusahaan')->first();
    }
}
