<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * The BW branch list: code => name.
     */
    private const STORES = [
        'BW0001' => 'BANACOM',
        'BW0002' => 'MARKETVIEW',
        'BW0003' => 'GAPAN',
        'BW0004' => 'LAPAZ',
        'BW0005' => 'PURA',
        'BW0006' => 'VICTORIA',
        'BW0007' => 'STA-ROSANE',
        'BW0008' => 'MH',
        'BW0009' => 'CUTCUT',
        'BW0010' => 'FTANEDO',
        'BW0011' => 'ANCHETA',
        'BW0012' => 'BUNO',
        'BW0013' => 'LUISITA',
        'BW0014' => 'CAPAS',
        'BW0015' => 'CONCEPCION',
        'BW0016' => 'WM-CAPAS',
        'BW0017' => 'WM-CONCEPCION',
        'BW0018' => 'ANGELES',
        'BW0019' => 'MABALACAT',
        'BW0020' => 'SAN-FERNANDO',
        'BW0022' => 'PANDACAQUI',
        'BW0023' => 'MAGALANG',
        'BW0026' => 'RCS-BULACAN',
        'BW0027' => 'SMG-BULACAN',
        'BW0028' => 'LUBAO',
        'BW0029' => 'FLORIDA',
        'BW0030' => 'OLONGAPO1',
        'BW0031' => 'WM-SUBIC',
        'BW0032' => 'SUBIC',
        'BW0033' => 'DINALUPIHAN',
        'BW0034' => 'WM-BALANGA',
        'BW0035' => 'ROB-HERMOSA',
        'BW0036' => 'HERMOZA',
        'BW0037' => 'WM-ANTIPOLO',
        'BW0038' => 'WM-EROD',
        'BW0041' => 'RAMOS',
        'BW0042' => 'PANIQUI',
        'BW0043' => 'CAMILING',
        'BW0044' => 'BAYAMBANG',
        'BW0045' => 'TAYUG',
        'BW0046' => 'VILLASIS',
        'BW0049' => 'SAN-CARLOS',
        'BW0053' => 'SAN-CARLOS-TC',
        'BW0061' => 'URDANETA2',
        'BW0062' => 'COMMUNITY',
        'BW0064' => 'OLONGAPO2',
        'BW0065' => 'CASTILLEJOS',
        'BW0066' => 'CRISTO REY',
        'BW0073' => 'GYRIES',
        'BW0074' => 'GYRIES2',
    ];

    /**
     * Sync the branch list into the database.
     *
     * Idempotent: upserts by code, then removes seeded stores whose code is
     * no longer listed. Manually added stores (no code) are left alone.
     */
    public function run(): void
    {
        foreach (self::STORES as $code => $name) {
            Store::updateOrCreate(['code' => $code], ['name' => $name]);
        }

        Store::whereNotNull('code')->whereNotIn('code', array_keys(self::STORES))->delete();
    }
}
