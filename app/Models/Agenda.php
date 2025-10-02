<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    public $timestamps = false;
    protected $table = 'agenda';
    protected $fillable = ['id', 'uid', 'kode', 'id_notulen', 'agenda', 'ket', 'status', 'created_by', 'created_date', 'changed_date'];

    public function get_notulen()
    {
        return  $this->hasOne(Notulen::class, 'id', 'id_notulen')->first();
    }
}
