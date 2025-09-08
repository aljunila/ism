<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Aturan extends Model
{
    public $timestamps = false;
    protected $table = 'form_aturan';
    protected $fillable = ['id', 'uid', 'kode', 'nama', 'isi', 'enforced_by', 'publish', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_enforced()
    {
        return  $this->hasOne(Karyawan::class, 'id', 'enforced_by')->first();
    }
}
