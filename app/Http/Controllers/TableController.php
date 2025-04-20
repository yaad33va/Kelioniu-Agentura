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
    public function edit($id)
    {
        // Fetch place data from the `užsakymai` and `kelionės` tables using raw SQL
        $place = DB::selectOne("
            SELECT u.*, k.pradžia, k.pabaiga, k.būsena
            FROM užsakymai u
            JOIN kelionės k ON u.fk_KELIONĖ = k.id
            WHERE u.užsakymo_numeris = ?
        ", [$id]);

        // Fetch ALL route points related to this `kelionės` ID using raw SQL
        $routePoints = DB::select("
            SELECT mt.*
            FROM maršruto_taškai mt
            WHERE mt.fk_KELIONĖ = ?
        ", [$place->fk_KELIONĖ]);

        // Fetch ALL landmarks related to the route points of this `kelionės` ID using raw SQL
        $landmarks = DB::select("
            SELECT lv.*
            FROM lankytinos_vietos lv
            WHERE lv.fk_MARŠRUTO_TAŠKAS IN (SELECT id FROM maršruto_taškai WHERE fk_KELIONĖ = ?)
        ", [$place->fk_KELIONĖ]);

        // Format dates for display
        $place->pradžia = \Carbon\Carbon::parse($place->pradžia)->format('Y-m-d');
        $place->pabaiga = \Carbon\Carbon::parse($place->pabaiga)->format('Y-m-d');
        $place->pasirašymo_data = \Carbon\Carbon::parse($place->pasirašymo_data)->format('Y-m-d');
        $place->nutraukimo_data = $place->nutraukimo_data ? \Carbon\Carbon::parse($place->nutraukimo_data)->format('Y-m-d') : null;

        return view('tables.edit', [
            'place' => $place,
            'routePoints' => $routePoints, // Pass all route points
            'landmarks' => $landmarks,        // Pass all landmarks
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validate the input
        $validatedData = $request->validate([
            // Existing validation rules
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration' => 'required|integer|min:1',
            'people_count' => 'required|integer|min:1',
            'trip_status' => 'required|string|max:255',
            'trip_start_date' => 'required|date',
            'trip_end_date' => 'nullable|date|after_or_equal:trip_start_date',

            // New fields for Maršruto taškas (expecting arrays)
            'country.*' => 'nullable|string|max:255',
            'city.*' => 'nullable|string|max:255',
            'name.*' => 'nullable|string|max:255',
            'address.*' => 'nullable|string|max:255',
            'route_duration.*' => 'nullable|integer|min:1',
            'route_point_id.*' => 'nullable|integer', // To identify existing route points

            // New fields for Lankytina vieta (expecting arrays)
            'working_hours.*' => 'nullable|string|max:255',
            'entry_fee.*' => 'nullable|numeric|min:0',
            'type.*' => 'nullable|string|max:255',
            'rating.*' => 'nullable|integer|min:0|max:5',
            'landmark_id.*' => 'nullable|integer', // To identify existing landmarks

            'delete_route_points' => 'nullable|array',
        ]);

        // Update the `užsakymai` table using raw SQL
        DB::update("
            UPDATE užsakymai
            SET pasirašymo_data = ?, nutraukimo_data = ?, trukmė = ?, žmonių_sk = ?
            WHERE užsakymo_numeris = ?
        ", [
            $validatedData['start_date'],
            $validatedData['end_date'],
            $validatedData['duration'],
            $validatedData['people_count'],
            $id,
        ]);

        // Fetch the related `kelionės` ID using raw SQL
        $tripIdResult = DB::selectOne("
            SELECT fk_KELIONĖ
            FROM užsakymai
            WHERE užsakymo_numeris = ?
        ", [$id]);

        if ($tripIdResult) {
            $tripId = $tripIdResult->fk_KELIONĖ;

            // Update the `kelionės` table using raw SQL
            DB::update("
                UPDATE kelionės
                SET būsena = ?, pradžia = ?, pabaiga = ?
                WHERE id = ?
            ", [
                $validatedData['trip_status'],
                $validatedData['trip_start_date'],
                $validatedData['trip_end_date'],
                $tripId,
            ]);

            // Handle deletions of Maršruto taškai and Lankytinos vietos
            if (isset($validatedData['delete_route_points']) && is_array($validatedData['delete_route_points'])) {
                $idsToDelete = array_filter($validatedData['delete_route_points']);
                if (!empty($idsToDelete)) {
                    DB::delete("
                        DELETE FROM lankytinos_vietos
                        WHERE fk_MARŠRUTO_TAŠKAS IN (" . implode(',', array_map('intval', $idsToDelete)) . ")
                    ");
                    DB::delete("
                        DELETE FROM maršruto_taškai
                        WHERE id IN (" . implode(',', array_map('intval', $idsToDelete)) . ")
                    ");
                }
            }

            // Insert/Update Maršruto taškai and Lankytinos vietos
            if (is_array($request->input('country'))) {
                foreach ($request->input('country') as $index => $country) {
                    $routePointId = $validatedData['route_point_id'][$index] ?? null;

                    if ($routePointId) {
                        // Update existing Maršruto taškai using raw SQL
                        DB::update("
                            UPDATE maršruto_taškai
                            SET valstybė = ?, miestas = ?, pavadinimas = ?, adresas = ?, trukmė = ?
                            WHERE id = ?
                        ", [
                            $validatedData['country'][$index] ?? null,
                            $validatedData['city'][$index] ?? null,
                            $validatedData['name'][$index] ?? null,
                            $validatedData['address'][$index] ?? null,
                            $validatedData['route_duration'][$index] ?? null,
                            $routePointId,
                        ]);

                        $landmarkId = $validatedData['landmark_id'][$index] ?? null;
                        if ($landmarkId) {
                            // Update existing Lankytinos vietos using raw SQL
                            DB::update("
                                UPDATE lankytinos_vietos
                                SET darbo_laikas = ?, įėjimo_mokestis = ?, tipas = ?, reitingas = ?
                                WHERE fk_MARŠRUTO_TAŠKAS = ?
                            ", [
                                $validatedData['working_hours'][$index] ?? null,
                                $validatedData['entry_fee'][$index] ?? null,
                                $validatedData['type'][$index] ?? null,
                                $validatedData['rating'][$index] ?? null,
                                $routePointId,
                            ]);
                        } else {
                            // Insert new Lankytinos vietos for the updated Maršruto taškas
                            DB::insert("
                                INSERT INTO lankytinos_vietos (fk_MARŠRUTO_TAŠKAS, darbo_laikas, įėjimo_mokestis, tipas, reitingas)
                                VALUES (?, ?, ?, ?, ?)
                            ", [
                                $routePointId,
                                $validatedData['working_hours'][$index] ?? null,
                                $validatedData['entry_fee'][$index] ?? null,
                                $validatedData['type'][$index] ?? null,
                                $validatedData['rating'][$index] ?? null,
                            ]);
                        }
                    } else {
                        // Insert a new Maršruto taškai record using raw SQL
                        DB::insert("
                            INSERT INTO maršruto_taškai (fk_KELIONĖ, valstybė, miestas, pavadinimas, adresas, trukmė)
                            VALUES (?, ?, ?, ?, ?, ?)
                        ", [
                            $tripId,
                            $validatedData['country'][$index] ?? null,
                            $validatedData['city'][$index] ?? null,
                            $validatedData['name'][$index] ?? null,
                            $validatedData['address'][$index] ?? null,
                            $validatedData['route_duration'][$index] ?? null,
                        ]);

                        $newRoutePointId = DB::getPdo()->lastInsertId();

                        // Insert a new Lankytinos vietos record using raw SQL
                        DB::insert("
                            INSERT INTO lankytinos_vietos (fk_MARŠRUTO_TAŠKAS, darbo_laikas, įėjimo_mokestis, tipas, reitingas)
                            VALUES (?, ?, ?, ?, ?)
                        ", [
                            $newRoutePointId,
                            $validatedData['working_hours'][$index] ?? null,
                            $validatedData['entry_fee'][$index] ?? null,
                            $validatedData['type'][$index] ?? null,
                            $validatedData['rating'][$index] ?? null,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('places')->with('success', 'Įrašas sėkmingai atnaujintas.');
    }

    public function destroy($id)
    {
        $place = DB::table('užsakymai')->where('užsakymo_numeris', $id)->first();

        if ($place) {
            // Delete related Lankytinos vietos using raw SQL
            DB::delete("
                DELETE lv
                FROM lankytinos_vietos lv
                JOIN maršruto_taškai mt ON lv.fk_MARŠRUTO_TAŠKAS = mt.id
                WHERE mt.fk_KELIONĖ = ?
            ", [$place->fk_KELIONĖ]);

            // Delete related Maršruto taškai using raw SQL
            DB::delete("
                DELETE FROM maršruto_taškai
                WHERE fk_KELIONĖ = ?
            ", [$place->fk_KELIONĖ]);

            // Delete Kelionė using raw SQL
            DB::delete("
                DELETE FROM kelionės
                WHERE id = ?
            ", [$place->fk_KELIONĖ]);

            // Delete Užsakymas using raw SQL
            DB::delete("
                DELETE FROM užsakymai
                WHERE užsakymo_numeris = ?
            ", [$id]);

            return redirect()->route('places')->with('success', 'Įrašas sėkmingai ištrintas.');
        }

        return redirect()->route('places')->with('error', 'Įrašo nepavyko rasti.');
    }

    public function createPlace(Request $request)
    {
        // Validate the input
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration' => 'required|integer|min:1',
            'people_count' => 'required|integer|min:1',
            'trip_status' => 'required|string|max:255',
            'trip_start_date' => 'required|date',
            'trip_end_date' => 'nullable|date|after_or_equal:trip_start_date',
            'country.*' => 'nullable|string|max:255',
            'city.*' => 'nullable|string|max:255',
            'name.*' => 'nullable|string|max:255',
            'address.*' => 'nullable|string|max:255',
            'route_duration.*' => 'nullable|integer|min:1',
            'working_hours.*' => 'nullable|string|max:255',
            'entry_fee.*' => 'nullable|numeric|min:0',
            'type.*' => 'nullable|string|max:255',
            'rating.*' => 'nullable|integer|min:0|max:5',
        ]);

        // Insert a new trip into the `kelionės` table
        DB::insert("
        INSERT INTO kelionės (būsena, pradžia, pabaiga)
        VALUES (?, ?, ?)
    ", [
            $validatedData['trip_status'],
            $validatedData['trip_start_date'],
            $validatedData['trip_end_date'],
        ]);

        // Get the last inserted trip ID
        $tripId = DB::getPdo()->lastInsertId();

        // Insert a new order into the `užsakymai` table
        DB::insert("
        INSERT INTO užsakymai (pasirašymo_data, nutraukimo_data, trukmė, žmonių_sk, fk_KELIONĖ)
        VALUES (?, ?, ?, ?, ?)
    ", [
            $validatedData['start_date'],
            $validatedData['end_date'],
            $validatedData['duration'],
            $validatedData['people_count'],
            $tripId,
        ]);

        // Handle Maršruto taškai (route points) and Lankytinos vietos (landmarks)
        if (is_array($request->input('country'))) {
            foreach ($request->input('country') as $index => $country) {
                // Insert a new route point into the `maršruto_taškai` table
                DB::insert("
                INSERT INTO maršruto_taškai (fk_KELIONĖ, valstybė, miestas, pavadinimas, adresas, trukmė)
                VALUES (?, ?, ?, ?, ?, ?)
            ", [
                    $tripId,
                    $validatedData['country'][$index] ?? null,
                    $validatedData['city'][$index] ?? null,
                    $validatedData['name'][$index] ?? null,
                    $validatedData['address'][$index] ?? null,
                    $validatedData['route_duration'][$index] ?? null,
                ]);

                // Get the last inserted route point ID
                $routePointId = DB::getPdo()->lastInsertId();

                // Insert a new landmark into the `lankytinos_vietos` table
                DB::insert("
                INSERT INTO lankytinos_vietos (fk_MARŠRUTO_TAŠKAS, darbo_laikas, įėjimo_mokestis, tipas, reitingas)
                VALUES (?, ?, ?, ?, ?)
            ", [
                    $routePointId,
                    $validatedData['working_hours'][$index] ?? null,
                    $validatedData['entry_fee'][$index] ?? null,
                    $validatedData['type'][$index] ?? null,
                    $validatedData['rating'][$index] ?? null,
                ]);
            }
        }

        return redirect()->route('places')->with('success', 'Naujas įrašas sėkmingai sukurtas.');
    }
    public function showCreatePlace(Request $request){
        return view('tables.newEntry');
    }
}
