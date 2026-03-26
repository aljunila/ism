<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'm_currency';
    protected $fillable = ['id', 'code', 'name', 'symbol', 'is_base', 'is_delete'];

    public function poRecords()
    {
        return $this->hasMany(PoBarang::class, 'id_currency', 'id');
    }

    public function purchasingRecords()
    {
        return $this->hasMany(PurchasingBarang::class, 'id_currency', 'id');
    }
}
