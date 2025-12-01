<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonditeDetail extends Model
{
    public $timestamps = false;
    protected $table = 'checklist_kondite_detail';
    protected $fillable = ['id', 'uid', 'kondite_id', 'checklist_item_id', 'value', 'ket', 'status', 'created_by', 'created_date'];

    public function get_data()
    {
        return  $this->hasOne(Kondite::class, 'id', 'kondite_id')->first();
    }

    public function get_item()
    {
        return  $this->hasOne(ChecklistItem::class, 'id', 'checklist_item_id')->first();
    }
}
