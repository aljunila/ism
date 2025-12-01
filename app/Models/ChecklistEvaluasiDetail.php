<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistEvaluasiDetail extends Model
{
    public $timestamps = false;
    protected $table = 'checklist_evaluasi_detail';
    protected $fillable = ['id', 'uid', 'evaluasi_id', 'checklist_item_id', 'value', 'status', 'created_by', 'created_date'];

    public function get_data()
    {
        return  $this->hasOne(Evaluasi::class, 'id', 'evaluasi_id')->first();
    }

    public function get_item()
    {
        return  $this->hasOne(ChecklistItem::class, 'id', 'checklist_item_id')->first();
    }

}
