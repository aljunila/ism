<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KirimOtp extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 't_kirim_otp';
    protected $fillable = [
        'id',
        'uid',
        'id_penerima',
        'otp_code',
        'expires_at',
        'used_at',
        'id_kirim',
        'is_delete',
        'created_by',
        'created_date',
        'changed_by',
        'changed_date',
    ];

    public function penerima()
    {
        return $this->belongsTo(User::class, 'id_penerima', 'id');
    }

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
