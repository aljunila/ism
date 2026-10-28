<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BBMKapal extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 't_bbm_kapal';
    protected $fillable = ['id', 'id_kapal', 'saldo', 'changed_by', 'changed_date'];

     public function get_kapal()
    {
        return  $this->hasOne(Kapal::class, 'id', 'id_kapal')->first();
    }

}
