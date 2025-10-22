<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetPassword extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'reset_password';
    protected $fillable = ['id', 'id_user', 'tgl_ajuan', 'tgl_reset', 'status'];

    public function get_user()
    {
        return  $this->hasOne(User::class, 'id', 'id_user')->first();
    }
}
