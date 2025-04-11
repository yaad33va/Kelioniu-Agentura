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
        if($name === 'užsakymai'){
            $records = DB::table($name)->select('užsakymo_numeris', 'pasirašymo_data', 'trukmė', 'žmonių_sk')->get();
            $headers = [
                'užsakymo_numeris' => 'Užsakymo numeris',
                'pasirašymo_data' => 'Pasirašymo data',
                'trukmė' => 'Trukmė (dienomis)',
                'žmonių_sk' => 'Žmonių skaičius'
            ];
        }
        else if($name === 'kelionės'){
            $records = DB::table($name)->select('pradžia', 'būsena')->get();
            $headers = [
                'pradžia' => 'Pradžia',
                'būsena' => 'Būsena'
            ];
        }
        else if($name === 'lankytinos_vietos'){
            $records = DB::table($name)->select('darbo_laikas', 'įėjimo_mokestis', 'reitingas', 'tipas')->get();
            $headers = [
                'darbo_laikas' => 'Darbo laikas',
                'įėjimo_mokestis' => 'Įėjimo mokestis',
                'reitingas' => 'Reitingas',
                'tipas' => 'Tipas'
            ];
        }
        else if($name === 'maršruto_taškai'){
            $records = DB::table($name)->select('valstybė', 'miestas', 'pavadinimas', 'adresas')->get();
            $headers = [
                'valstybė' => 'Valstybė',
                'miestas' => 'Miestas',
                'pavadinimas' => 'Pavadinimas',
                'adresas' => 'Adresas'
            ];
        }
        else if($name === 'viešbučiai'){
            $records = DB::table($name)->select('kaina_nakčiai', 'reitingas', 'svečių_sk', 'tel_nr')->get();
            $headers = [
                'kaina_nakčiai' => 'Kaina nakčiai',
                'reitingas' => 'Reitingas',
                'svečių_sk' => 'Svečių skaičius',
                'tel_nr' => 'Telefono numeris'
            ];
        }
        else {
            $records = DB::table($name)->get();
            $headers = null;
        }
        return view('tables.show',
        [
            'name' => $name,
            'records' => $records,
            'headers' => $headers,
        ]);
    }
}

