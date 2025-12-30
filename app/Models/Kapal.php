<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use Carbon\Carbon;

class Kapal extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'kapal';
    protected $fillable = ['id', 'uid', 'nama', 'pendaftaran', 'no_siup', 'no_akte', 'dikeluarkan_di', 'selar',
                            'pemilik', 'call_sign', 'galangan', 'konstruksi', 'type', 'loa', 'lbp', 'lebar', 
                            'dalam', 'summer_draft', 'winter_draft', 'draft_air_tawar', 'tropical_draft', 'isi_kotor',
                            'bobot_mati', 'nt', 'merk_mesin_induk', 'tahun_mesin_induk', 'no_mesin_induk', 'merk_mesin_bantu', 
                            'tahun_mesin_bantu', 'no_mesin_bantu', 'max_speed', 'normal_speed', 'min_speed', 'bahan_bakar',
                            'jml_butuh', 'berkas', 'status', 'created_by', 'created_date', 'changed_by', 'changed_date', 'id_cabang', 'gol'];

    public function get_pemilik()
    {
        return  $this->hasOne(Perusahaan::class, 'id', 'pemilik')->first();
    }

    public function get_cabang()
    {
        return  $this->hasOne(Cabang::class, 'id', 'id_cabang')->first();
    }
}
