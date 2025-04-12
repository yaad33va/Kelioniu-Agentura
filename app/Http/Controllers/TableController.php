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
            mt.adresas,
            lv.id
        FROM lankytinos_vietos lv
        JOIN maršruto_taškai mt ON lv.fk_MARŠRUTO_TAŠKAS = mt.id
    ");

        return view('tables.places', ['places' => $places]);
    }
    public function showPlace($id)
    {
        $place = DB::select(
            'SELECT
            lv.id AS lv_id, lv.darbo_laikas, lv.įėjimo_mokestis, lv.reitingas, lv.tipas,
            mt.valstybė, mt.miestas, mt.pavadinimas, mt.adresas, mt.trukmė, mt.id AS fk_MARŠRUTO_TAŠKAS,
            uz.užsakymo_numeris, uz.pasirašymo_data, uz.nutraukimo_data, uz.trukmė AS užsakymo_trukmė, uz.žmonių_sk,
            kel.pradžia, kel.pabaiga, kel.būsena, kel.id AS fk_KELIONĖ
         FROM lankytinos_vietos lv
         JOIN maršruto_taškai mt ON lv.fk_MARŠRUTO_TAŠKAS = mt.id
         JOIN užsakymai uz ON uz.fk_KELIONĖ = mt.fk_KELIONĖ
         JOIN kelionės kel ON uz.fk_KELIONĖ = kel.id
         WHERE lv.id = ?',
            [$id]
        );

        // Check if the place exists
        if (empty($place)) {
            abort(404, 'Įrašas nerastas.');
        }

        // Pass the first result to the view
        return view('tables.edit', ['place' => $place[0]]);
    }

    public function updatePlace(Request $request, $id)
    {
        $request->validate([
            'darbo_laikas'      => 'required|string|max:20',
            'įėjimo_mokestis'   => 'required|integer',
            'reitingas'         => 'required|integer',
            'tipas'             => 'required|string',
            'valstybė'          => 'required|string|max:50',
            'miestas'           => 'required|string|max:50',
            'pavadinimas'       => 'required|string|max:100',
            'adresas'           => 'required|string|max:100',
            'trukmė'            => 'required|integer',
            'užsakymo_numeris'  => 'nullable|integer',
            'nutraukimo_data'   => 'nullable|date',
        ]);

        // Update MARŠRUTO_TAŠKAI table
        DB::update(
            'UPDATE maršruto_taškai
         SET valstybė = ?, miestas = ?, pavadinimas = ?, adresas = ?, trukmė = ?
         WHERE id = ?',
            [
                $request->valstybė,
                $request->miestas,
                $request->pavadinimas,
                $request->adresas,
                $request->trukmė,
                $request->fk_MARŠRUTO_TAŠKAS,
            ]
        );

        // Update LANKYTINOS_VIETOS table
        DB::update(
            'UPDATE lankytinos_vietos
         SET darbo_laikas = ?, įėjimo_mokestis = ?, reitingas = ?, tipas = ?
         WHERE id = ?',
            [
                $request->darbo_laikas,
                $request->įėjimo_mokestis,
                $request->reitingas,
                $request->tipas,
                $id,
            ]
        );

        // Update UŽSAKYMAI table if provided
        if ($request->has('užsakymo_numeris')) {
            DB::update(
                'UPDATE užsakymai
             SET trukmė = ?, žmonių_sk = ?, nutraukimo_data = ?
             WHERE užsakymo_numeris = ?',
                [
                    $request->trukmė,
                    $request->žmonių_sk,
                    $request->nutraukimo_data,
                    $request->užsakymo_numeris,
                ]
            );
        }

        // Update KELIONĖS table
        DB::update(
            'UPDATE kelionės
         SET pradžia = ?, pabaiga = ?, būsena = ?
         WHERE id = ?',
            [
                $request->pradžia,
                $request->pabaiga,
                $request->būsena,
                $request->fk_KELIONĖ,
            ]
        );

        // Redirect back with a success message
        return redirect()->route('places');
    }

    public function destroy($id)
    {
        // Get the foreign key reference from LANKYTINOS_VIETOS table (fk_MARŠRUTO_TAŠKAS)
        $routePointId = DB::select(
            'SELECT fk_MARŠRUTO_TAŠKAS FROM lankytinos_vietos WHERE id = ?',
            [$id]
        );

        // Check if the record exists
        if (empty($routePointId)) {
            abort(404, 'Įrašas nerastas.');
        }

        // Delete the record from the LANKYTINOS_VIETOS table
        DB::delete(
            'DELETE FROM lankytinos_vietos WHERE id = ?',
            [$id]
        );

        // Delete the associated record from the MARŠRUTO_TAŠKAI table
        DB::delete(
            'DELETE FROM maršruto_taškai WHERE id = ?',
            [$routePointId[0]->fk_MARŠRUTO_TAŠKAS]
        );

        // Redirect back with a success message
        return redirect()->route('places')->with('success', 'Įrašas sėkmingai ištrintas.');
    }

    public function createPlace(){

    }
}

