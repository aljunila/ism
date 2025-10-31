<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aturan extends Model
{
    public $timestamps = false;
    protected $table = 'form_aturan';
    protected $fillable = ['id', 'uid', 'kode', 'id_perusahaan', 'nama', 'isi', 'enforced_by', 'file', 'publish', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_enforced()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'enforced_by')->first();
    }

    public function get_perusahaan()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'id_perusahaan')->first();
    }
}
