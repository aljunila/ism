<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistDataDetail extends Model
{
    public $timestamps = false;
    protected $table = 'checklist_data_detail';
    protected $fillable = ['id', 'uid', 'checklist_data_id', 'ket', 'status', 'created_by', 'created_date'];

    public function get_data()
    {
        return  $this->hasOne(ChecklistData::class, 'id', 'checklist_data_id')->first();
    }
}
