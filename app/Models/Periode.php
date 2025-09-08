<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Periode extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'periode';
    protected $fillable = ['id', 'no', 'nama', 'tgl_mulai', 'tgl_akhir', 'status', 'publish', 'id_cabang', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function setTglMulaiAttribute($value) {
        $this->attributes['tgl_mulai'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    public function setTglAkhirAttribute($value) {
        $this->attributes['tgl_akhir'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    public function getTglMulaiAttribute() {
        return Carbon::createFromFormat('Y-m-d', $this->attributes['tgl_mulai'])->format('d/m/Y');
    }

    public function getTglAkhirAttribute() {
        return Carbon::createFromFormat('Y-m-d', $this->attributes['tgl_akhir'])->format('d/m/Y');
    }

    public function get_cabang()
    {
        return  $this->hasOne(Cabang::class, 'id', 'id_cabang')->first();
    }
}
