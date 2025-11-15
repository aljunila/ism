<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    public $timestamps = false;
    protected $table = 'checklist_item';
    protected $fillable = ['id', 'uid', 'kode', 'item', 'parent_id', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

}
