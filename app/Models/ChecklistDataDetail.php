<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistDataDetail extends Model
{
    public $timestamps = false;
    protected $table = 'checklist_data_detail';
    protected $fillable = ['id', 'uid', 'checklist_data_id', 'bbm_id', 'checklist_item_id', 'value', 'ket', 'status', 'created_by', 'created_date'];

    public function get_data()
    {
        return  $this->hasOne(ChecklistData::class, 'id', 'checklist_data_id')->first();
    }

    public function get_item()
    {
        return  $this->hasOne(ChecklistItem::class, 'id', 'checklist_item_id')->first();
    }

    public function get_bbm()
    {
        return  $this->hasOne(ChecklistData::class, 'id', 'bbm_id')->first();
    }
}
