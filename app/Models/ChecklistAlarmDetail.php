<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistAlarmDetail extends Model
{
    public $timestamps = false;
    protected $table = 'checklist_alarm_detail';
    protected $fillable = ['id', 'uid', 'checklist_data_id', 'checklist_item_id', 'periode', 'uji_terakhir', 'uji_yad', 'ket', 'status', 'created_by', 'created_date'];

    public function get_data()
    {
        return  $this->hasOne(ChecklistData::class, 'id', 'checklist_data_id')->first();
    }

    public function get_item()
    {
        return  $this->hasOne(ChecklistItem::class, 'id', 'checklist_item_id')->first();
    }
}
