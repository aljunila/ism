<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Checklist508 extends Model
{
    public $timestamps = false;
    protected $table = 'checklist_detail_508';
    protected $fillable = ['id', 'uid', 'checklist_data_id', 'nama', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date'];

    public function get_data()
    {
        return  $this->hasOne(ChecklistData::class, 'id', 'checklist_data_id')->first();
    }
}
