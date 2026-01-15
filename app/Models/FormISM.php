<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormISM extends Model
{
    public $timestamps = false;
    protected $table = 't_ism';
    protected $fillable = ['id', 'uid',  'id_form', 'id_perusahaan', 'judul', 'is_delete', 'created_by', 'created_date', 'changed_by', 'changed_date'];

}
