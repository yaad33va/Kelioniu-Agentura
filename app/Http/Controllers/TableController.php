<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    public function index()
    {
        // Include only these tables
        $includedTables = [
            'užsakymai', 'kelionės', 'lankytinos_vietos', 'maršruto_taškai',
        ];

        return view('tables.index', ['tableNames' => $includedTables]);
    }

    public function show($name)
    {
        if ($name === 'užsakymai') {
            $records = DB::select("
            SELECT užsakymo_numeris, pasirašymo_data, nutraukimo_data, trukmė, žmonių_sk
            FROM užsakymai
        ");
            $headers = [
                'užsakymo_numeris' => 'Užsakymo numeris',
                'pasirašymo_data' => 'Pasirašymo data',
                'nutraukimo_data' => 'Nutraukimo data',
                'trukmė' => 'Trukmė (dienomis)',
                'žmonių_sk' => 'Žmonių skaičius'
            ];
        } else if ($name === 'kelionės') {
            $records = DB::select("
            SELECT pradžia, pabaiga, būsena
            FROM kelionės
        ");
            $headers = [
                'pradžia' => 'Pradžia',
                'pabaiga' => 'Pabaiga',
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
            SELECT valstybė, miestas, pavadinimas, adresas, trukmė
            FROM maršruto_taškai
        ");
            $headers = [
                'valstybė' => 'Valstybė',
                'miestas' => 'Miestas',
                'pavadinimas' => 'Pavadinimas',
                'adresas' => 'Adresas',
                'trukmė' => 'Trukmė'
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
                u.užsakymo_numeris AS record_id,
                u.pasirašymo_data AS start_date,
                u.nutraukimo_data AS end_date,
                u.trukmė AS duration,
                u.žmonių_sk AS people_count,
                k.pradžia AS trip_start_date,
                k.pabaiga AS trip_end_date,
                k.būsena AS status,
                'combined' AS record_type
            FROM užsakymai u
            JOIN kelionės k ON u.fk_KELIONĖ = k.id
        ");

        return view('tables.places', ['places' => $places]);
    }
    public function update(Request $request, $id)
    {
        // Validate the input fields
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration' => 'required|integer|min:1',
            'people_count' => 'required|integer|min:1',
            'trip_status' => 'required|string|max:255',
            'trip_start_date' => 'required|date',
            'trip_end_date' => 'required|date|after_or_equal:trip_start_date',
        ]);

        // Update the `užsakymai` table
        DB::update('
        UPDATE užsakymai
        SET pasirašymo_data = ?, nutraukimo_data = ?, žmonių_sk = ?, trukmė = ?
        WHERE užsakymo_numeris = ?
    ', [
            $validatedData['start_date'],
            $validatedData['end_date'],
            $validatedData['people_count'],
            $validatedData['duration'],
            $id,
        ]);

        // Update the `kelionės` table
        DB::update('
        UPDATE kelionės
        SET būsena = ?, pradžia = ?, pabaiga = ?
        WHERE id = (SELECT fk_KELIONĖ FROM užsakymai WHERE užsakymo_numeris = ?)
    ', [
            $validatedData['trip_status'],
            $validatedData['trip_start_date'],
            $validatedData['trip_end_date'],
            $id,
        ]);

        // Redirect back to the main page with a success message
        return redirect()->route('places')->with('success', 'Įrašas sėkmingai atnaujintas.');
    }
    public function edit($id)
    {
        $place = DB::selectOne('
        SELECT u.*, k.*
        FROM užsakymai u
        JOIN kelionės k ON u.fk_KELIONĖ = k.id
        WHERE u.užsakymo_numeris = ?
    ', [$id]);

        if (!$place) {
            abort(404, 'Įrašas nerastas.');
        }

        // Format dates
        $place->pradžia = \Carbon\Carbon::parse($place->pradžia)->format('Y-m-d');
        $place->pabaiga = \Carbon\Carbon::parse($place->pabaiga)->format('Y-m-d');
        $place->pasirašymo_data = \Carbon\Carbon::parse($place->pasirašymo_data)->format('Y-m-d');
        $place->nutraukimo_data = \Carbon\Carbon::parse($place->nutraukimo_data)->format('Y-m-d');

        return view('tables.edit', ['place' => $place]);
    }
    public function destroy($id)
    {
        // Perform deletion query
        DB::delete("DELETE FROM užsakymai WHERE užsakymo_numeris = ?", [$id]);

        // Redirect back with a success message
        return redirect()->route('places')->with('success', 'Įrašas sėkmingai ištrintas.');
    }

    public function createPlace(){

    }
}

