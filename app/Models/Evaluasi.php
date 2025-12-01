<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    public $timestamps = false;
    protected $table = 'evaluasi';
    protected $fillable = ['id', 'uid', 'checklist_data_id', 'tanggal', 'ket', 'note', 'id_nahkoda', 'id_instruktur', 'id_kepala', 'status', 'created_by', 'created_date'];

    public function get_data()
    {
        return  $this->hasOne(ChecklistData::class, 'id', 'checklist_data_id')->first();
    }

    public function get_nahkoda()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_nahkoda')->first();
    }

    public function get_instruktur()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_instruktur')->first();
    }

    public function get_kepala()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'id_kepala')->first();
    }
}
