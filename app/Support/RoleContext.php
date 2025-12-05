<?php

namespace App\Support;

use Illuminate\Support\Facades\Session;

class RoleContext
{
    /**
     * Ambil konteks role yang sudah dimapping dari role/jenis.
     * Return array: jenis (1=superadmin, 2=admin perusahaan, 3=user kapal, 4=karyawan),
     * is_superadmin, id_perusahaan, id_kapal.
     */
    public static function get(): array
    {
        $jenis = (int) Session::get('previllage', 4);
        $isSuper = $jenis === 1;

        return [
            'jenis' => $jenis,
            'is_superadmin' => $isSuper,
            'perusahaan_id' => Session::get('id_perusahaan'),
            'kapal_id' => Session::get('id_kapal'),
        ];
    }
}
