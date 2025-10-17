<?php

namespace App\Exports;

use App\Models\Perusahaan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class PerusahaanExport implements FromView
{

    public function __construct()
    {
        
    }

   public function view(): View
    {
        return view('export.perusahaan', [
            'data' => Perusahaan::where('status','A')->get()
        ]);
    }
}
