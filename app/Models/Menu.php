<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    // use HasFactory;
    public $timestamps = false;
    protected $table = 'menu';
    protected $fillable = ['id', 'nama', 'kode', 'link', 'icon', 'id_parent', 'no', 'status'];

    public function children()
    {
        return $this->hasMany(Menu::class, 'id_parent', 'id')
            ->where('status', 'A')
            ->orderBy('no', 'ASC');
    }
}
