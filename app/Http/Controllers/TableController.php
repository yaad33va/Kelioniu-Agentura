<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    public function index()
    {
        // Include only these tables
        $includedTables = [
            'užsakymai', 'kelionės', 'lankytinos_vietos', 'maršruto_taškai', 'viešbučiai',
        ];

        return view('tables.index', ['tableNames' => $includedTables]);
    }

    public function show($name)
    {
        if ($name === 'užsakymai') {
            $records = DB::select("
            SELECT užsakymo_numeris, pasirašymo_data, trukmė, žmonių_sk
            FROM užsakymai
        ");
            $headers = [
                'užsakymo_numeris' => 'Užsakymo numeris',
                'pasirašymo_data' => 'Pasirašymo data',
                'trukmė' => 'Trukmė (dienomis)',
                'žmonių_sk' => 'Žmonių skaičius'
            ];
        } else if ($name === 'kelionės') {
            $records = DB::select("
            SELECT pradžia, būsena
            FROM kelionės
        ");
            $headers = [
                'pradžia' => 'Pradžia',
                'būsena' => 'Būsena'
            ];
        } else if ($name === 'lankytinos_vietos') {
            $records = DB::select("
            SELECT darbo_laikas, įėjimo_mokestis, reitingas, tipas
            FROM lankytinos_vietos
        ");
            $headers = [
                'darbo_laikas' => 'Darbo laikas',
                'įėjimo_mokestis' => 'Įėjimo mokestis',
                'reitingas' => 'Reitingas',
                'tipas' => 'Tipas'
            ];
        } else if ($name === 'maršruto_taškai') {
            $records = DB::select("
            SELECT valstybė, miestas, pavadinimas, adresas
            FROM maršruto_taškai
        ");
            $headers = [
                'valstybė' => 'Valstybė',
                'miestas' => 'Miestas',
                'pavadinimas' => 'Pavadinimas',
                'adresas' => 'Adresas'
            ];
        } else if ($name === 'viešbučiai') {
            $records = DB::select("
            SELECT kaina_nakčiai, reitingas, svečių_sk, tel_nr
            FROM viešbučiai
        ");
            $headers = [
                'kaina_nakčiai' => 'Kaina nakčiai',
                'reitingas' => 'Reitingas',
                'svečių_sk' => 'Svečių skaičius',
                'tel_nr' => 'Telefono numeris'
            ];
        } else {
            $records = DB::select("SELECT * FROM `$name`");
            $headers = null;
        }

        return view('tables.show', [
            'name' => $name,
            'records' => $records,
            'headers' => $headers,
        ]);
    }

    public function places()
    {
        $places = DB::select("
        SELECT
            lv.darbo_laikas,
            lv.įėjimo_mokestis,
            lv.reitingas,
            lv.tipas,
            mt.valstybė,
            mt.miestas,
            mt.pavadinimas AS taško_pavadinimas,
            mt.adresas
        FROM lankytinos_vietos lv
        JOIN maršruto_taškai mt ON lv.fk_MARŠRUTO_TAŠKAS = mt.id
    ");

        return view('tables.places', ['places' => $places]);
    }



}

