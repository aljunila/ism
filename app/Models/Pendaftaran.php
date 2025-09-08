<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Pendaftaran extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'pendaftaran';
    protected $fillable = ['id', 'nama', 'id_periode', 'fee', 'tgl_mulai', 'tgl_akhir', 'id_sekolah', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_periode()
    {
        return  $this->hasOne(Periode::class, 'id', 'id_periode')->first();
    }

  
    public function get_sekolah()
    {
        return  $this->hasOne(Sekolah::class, 'id', 'id_sekolah')->first();
    }
}
