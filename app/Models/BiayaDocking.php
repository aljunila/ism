<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class BiayaDocking extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 't_biaya_docking';
    protected $fillable = ['id', 'uid', 'id_docking', 'id_subjob', 'deskripsi', 'unit', 'volume', 'harga', 'total', 'keterangan', 'is_delete', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_docking()
    {
        return  $this->hasOne(Docking::class, 'id', 'id_docking')->first();
    }

     public function get_subjob()
    {
        return  $this->hasOne(SubJobDock::class, 'id', 'id_subjob')->first();
    }
}
