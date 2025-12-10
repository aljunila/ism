<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model
{
    public $timestamps = false;
    protected $table = 'role_menu';
    protected $fillable = ['role_id', 'menu_id'];
}
