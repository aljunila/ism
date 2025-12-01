<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewDetail extends Model
{
    public $timestamps = false;
    protected $table = 'checklist_interview_detail';
    protected $fillable = ['id', 'uid', 'interview_id', 'checklist_item_id', 'value', 'ket', 'status', 'created_by', 'created_date'];

    public function get_data()
    {
        return  $this->hasOne(Interview::class, 'id', 'interview_id')->first();
    }

    public function get_item()
    {
        return  $this->hasOne(ChecklistItem::class, 'id', 'checklist_item_id')->first();
    }
}
