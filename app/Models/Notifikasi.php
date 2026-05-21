<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 't_notifikasi';
    protected $fillable = [
        'id',
        'uid',
        'id_user',
        'tipe',
        'judul',
        'pesan',
        'url',
        'read_at',
        'is_delete',
        'created_by',
        'created_date',
        'changed_by',
        'changed_date',
    ];
}
