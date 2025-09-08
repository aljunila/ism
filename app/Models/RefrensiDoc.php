<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class RefrensiDoc extends Model
{
    public $timestamps = false;
    protected $table = 'refrensi_doc';
    protected $fillable = ['id', 'uid', 'kode',  'nama_doc', 'edisi', 'id_pj', 'lokasi', 'file', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_pj()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_pj')->first();
    }
}
