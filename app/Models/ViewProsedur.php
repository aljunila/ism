<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class ViewProsedur extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'view_prosedur';
    protected $fillable = ['id', 'id_user', 'id_prosedur', 'jml_lihat', 'jml_download', 'update_lihat', 'update_download'];

    public function get_user()
    {
        return  $this->hasOne(User::class, 'id', 'id_user')->first();
    }

    public function get_prosedur()
    {
        return  $this->hasOne(Prosedur::class, 'id', 'id_prosedur')->first();
    }
}
