<?php

namespace App\Exports;

use App\Models\Gudang;
use App\Models\Barang;
use App\Models\KelBarang;
use App\Models\Kapal;
use App\Models\LogGudang;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class LapGudangExport implements FromView
{
    protected $id;
    protected $start;
    protected $end;

    public function __construct($id, $start, $end)
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
    }

   public function view(): View
    {
        $kapal = Kapal::where('id', $this->id)->first();
        $namakapal = $kapal->nama;
        $kategori = KelBarang::where('is_delete', 0)->get();

        $kategori = $kategori->filter(function ($kel) {
            $kel->barang = DB::table('t_gudang as a')
                            ->leftJoin('m_barang as b', 'a.id_barang', '=', 'b.id')
                            ->select('a.*', 'b.nama', 'b.kode', 'b.deskripsi as des')
                            ->where('b.id_kel_barang', $kel->id)->where('b.is_delete', 0)
                            ->where('a.id_kapal', $this->id)
                            ->get();
            foreach($kel->barang as $barang){
                $barang->riwayat = LogGudang::where('id_gudang', $barang->id)->whereBetween('tanggal', [$this->start, $this->end])->orderBy('tanggal')->get();
            }
            return $kel->barang->isNotEmpty();
        });
        $maxRiwayat = DB::table('t_log_gudang as a')
                    ->leftJoin('t_gudang as b', 'a.id_gudang', '=', 'b.id')
                    ->where('b.id_kapal', $this->id)
                    ->whereBetween('a.tanggal', [$this->start, $this->end])
                    ->groupBy('b.id_barang')
                    ->selectRaw('COUNT(*) as total')
                    ->get()
                    ->max('total');

        return view('export.lapgudang',compact('kategori', 'maxRiwayat', 'namakapal'));
    }
}
