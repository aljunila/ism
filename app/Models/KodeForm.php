<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KodeForm extends Model
{
    public $timestamps = false;
    protected $table = 'kode_form';
    protected $fillable = ['id', 'kode',  'nama', 'ket', 'intruksi'];

}
