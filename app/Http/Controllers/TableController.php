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
        // Gauti vietos duomenis
        $place = DB::select("
            SELECT
                lv.id AS lv_id,
                lv.darbo_laikas,
                lv.įėjimo_mokestis,
                lv.reitingas,
                lv.tipas,
                mt.valstybė,
                mt.miestas,
                mt.pavadinimas,
                mt.adresas,
                mt.trukmė,
                mt.id AS fk_MARŠRUTO_TAŠKAS
            FROM lankytinos_vietos lv
            JOIN maršruto_taškai mt ON lv.fk_MARŠRUTO_TAŠKAS = mt.id
            WHERE lv.id = ?
        ", [$id]);

        if (empty($place)) {
            abort(404, 'Įrašas nerastas.');
        }

        // Gauti keliones, susijusias su vieta per užsakymus
        $keliones = DB::select("
            SELECT DISTINCT k.*
            FROM kelionės k
            JOIN užsakymai u ON k.id = u.fk_KELIONĖ
            JOIN maršruto_taškai mt ON u.fk_KELIONĖ = mt.fk_KELIONĖ
            WHERE mt.id = ?
        ", [$place[0]->fk_MARŠRUTO_TAŠKAS]);

        // Gauti užsakymus kiekvienai kelionei
        foreach ($keliones as $kelione) {
            $kelione->užsakymai = DB::select("
                SELECT u.*
                FROM užsakymai u
                WHERE u.fk_KELIONĖ = ?
            ", [$kelione->id]);
        }

        return view('tables.edit', ['place' => $place[0], 'keliones' => $keliones]);
    }
    public function updatePlace(Request $request, $id)
    {
        $request->validate([
            'darbo_laikas' => 'required|string|max:20',
            'įėjimo_mokestis' => 'required|decimal:2',
            'reitingas' => 'required|integer',
            'tipas' => 'required|string',
            'valstybė' => 'required|string|max:50',
            'miestas' => 'required|string|max:50',
            'pavadinimas' => 'required|string|max:100',
            'adresas' => 'required|string|max:100',
            'trukmė' => 'required|integer',
            'kelionės' => 'nullable|array',
            'kelionės.*.id' => 'integer',
            'kelionės.*.pradžia' => 'date',
            'kelionės.*.pabaiga' => 'date',
            'kelionės.*.būsena' => 'string',
            'kelionės.*.method' => 'string',
            'kelionės.*.užsakymai' => 'nullable|array',
            'kelionės.*.užsakymai.*.id' => 'nullable|integer',
            'kelionės.*.užsakymai.*.trukmė' => 'required|integer',
            'kelionės.*.užsakymai.*.žmonių_sk' => 'required|integer',
            'kelionės.*.užsakymai.*.nutraukimo_data' => 'date',
            'kelionės.*.užsakymai.*.method' => 'string',
        ]);

        debug($request->all());

        // Atnaujinami MARŠRUTO_TAŠKAI ir LANKYTINOS_VIETOS įrašai
        DB::update(
            'UPDATE maršruto_taškai SET valstybė = ?, miestas = ?, pavadinimas = ?, adresas = ?, trukmė = ? WHERE id = ?',
            [
                $request->valstybė,
                $request->miestas,
                $request->pavadinimas,
                $request->adresas,
                $request->trukmė,
                $request->fk_MARŠRUTO_TAŠKAS,
            ]
        );

        DB::update(
            'UPDATE lankytinos_vietos SET darbo_laikas = ?, įėjimo_mokestis = ?, reitingas = ?, tipas = ? WHERE id = ?',
            [
                $request->darbo_laikas,
                $request->įėjimo_mokestis,
                $request->reitingas,
                $request->tipas,
                $id,
            ]
        );

        // Tvarkomi Kelionės ir Užsakymai naudojant gryną SQL
        if ($request->has('keliones')) {
            foreach ($request->keliones as $kelioneData) {
                debug('kelioneData', $kelioneData);
                if (isset($kelioneData['id'])) {
                    if ($kelioneData['method'] === "delete") {
                        // Ištrinama esama Kelionė
                        DB::delete('DELETE FROM kelionės WHERE id = ?', [$kelioneData['id']]);
                        continue; // Pereiname prie kitos kelionės
                    }

                    // Atnaujinama esama Kelionė
                    DB::update(
                        'UPDATE kelionės SET pradžia = ?, pabaiga = ?, būsena = ? WHERE id = ?',
                        [
                            $kelioneData['pradžia'],
                            $kelioneData['pabaiga'],
                            $kelioneData['būsena'],
                            $kelioneData['id'],
                        ]
                    );
                    $kelioneId = $kelioneData['id']; // Naudojamas esamas ID Užsakymams
                } else if ($kelioneData['method'] === 'new') {
                    // Sukuriama nauja Kelionė
                    DB::insert(
                        'INSERT INTO kelionės (pradžia, pabaiga, būsena) VALUES (?, ?, ?)',
                        [
                            $kelioneData['pradžia'],
                            $kelioneData['pabaiga'],
                            $kelioneData['būsena'],
                        ]
                    );
                    $kelioneId = DB::getPdo()->lastInsertId(); // Gaunamas naujos Kelionės ID
                } else {
                    continue;
                }

                // Tvarkomi Užsakymai šiai Kelionei
                if (isset($kelioneData['užsakymai'])) {
                    foreach ($kelioneData['užsakymai'] as $uzsakymasData) {
                        if (isset($uzsakymasData['id'])) {
                            if ($uzsakymasData['method'] === 'delete') {
                                // Ištrinamas esamas Užsakymas
                                DB::delete('DELETE FROM užsakymai WHERE užsakymo_numeris = ?', [$uzsakymasData['id']]);
                                continue; // Pereiname prie kito užsakymo
                            }

                            // Atnaujinamas esamas Užsakymas
                            DB::update(
                                'UPDATE užsakymai SET trukmė = ?, žmonių_sk = ?, nutraukimo_data = ? WHERE užsakymo_numeris = ?',
                                [
                                    $uzsakymasData['trukmė'],
                                    $uzsakymasData['žmonių_sk'],
                                    $uzsakymasData['nutraukimo_data'],
                                    $uzsakymasData['id'],
                                ]
                            );
                        } else if ($uzsakymasData['method'] === 'new') {
                            // Sukuriamas naujas Užsakymas ir susiejamas su Kelione
                            DB::insert(
                                'INSERT INTO užsakymai (trukmė, žmonių_sk, nutraukimo_data, fk_KELIONĖ) VALUES (?, ?, ?, ?)',
                                [
                                    $uzsakymasData['trukmė'],
                                    $uzsakymasData['žmonių_sk'],
                                    $uzsakymasData['nutraukimo_data'],
                                    $kelioneId, // Naudojamas Kelionės ID
                                ]
                            );
                        }
                    }
                }
            }
        }

        // Nukreipiama atgal su sėkmės pranešimu
        return redirect()->route('places')->with('success', 'Įrašas sėkmingai atnaujintas.');
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

