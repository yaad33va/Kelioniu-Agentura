<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    public function index()
    {
        //Kurias lenteles rodyt
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
            $headers = null;    //Jei neegzistuoja tokia lentele
        }

        return view('tables.show', [
            'name' => $name,
            'records' => $records,
            'headers' => $headers,
        ]);
    }

    //Sujungiamos lenteles uzsakymai ir keliones
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
        //Vienas uzsakymas keliones
        $place = DB::selectOne("
            SELECT u.*, k.pradžia, k.pabaiga, k.būsena
            FROM užsakymai u
            JOIN kelionės k ON u.fk_KELIONĖ = k.id
            WHERE u.užsakymo_numeris = ?
        ", [$id]);

        //marsruto taskai ieinantys i kelione
        $routePoints = DB::select("
            SELECT mt.*
            FROM maršruto_taškai mt
            WHERE mt.fk_KELIONĖ = ?
        ", [$place->fk_KELIONĖ]);

        //lankytinos vietos ieinancios i kelione
        $landmarks = DB::select("
            SELECT lv.*
            FROM lankytinos_vietos lv
            WHERE lv.fk_MARŠRUTO_TAŠKAS IN (SELECT id FROM maršruto_taškai WHERE fk_KELIONĖ = ?)
        ", [$place->fk_KELIONĖ]);

        //Kad normaliai data parodytu
        $place->pradžia = \Carbon\Carbon::parse($place->pradžia)->format('Y-m-d');
        $place->pabaiga = \Carbon\Carbon::parse($place->pabaiga)->format('Y-m-d');
        $place->pasirašymo_data = \Carbon\Carbon::parse($place->pasirašymo_data)->format('Y-m-d');
        $place->nutraukimo_data = $place->nutraukimo_data ? \Carbon\Carbon::parse($place->nutraukimo_data)->format('Y-m-d') : null;

        return view('tables.edit', [
            'place' => $place,
            'routePoints' => $routePoints,
            'landmarks' => $landmarks,
        ]);
    }

    public function update(Request $request, $id)
    {
        //Visu irasu validacija
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
            'route_point_id.*' => 'nullable|integer',   //pagal id, bet pasleptas

            'working_hours.*' => 'nullable|string|max:255',
            'entry_fee.*' => 'nullable|numeric|min:0',
            'type.*' => 'nullable|string|max:255',
            'rating.*' => 'nullable|integer|min:0|max:5',
            'landmark_id.*' => 'nullable|integer',  //pagal id, bet pasleptas

            'delete_route_points' => 'nullable|array',
        ]);

        //Paredaguojam uzsakymus
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

        $tripIdResult = DB::selectOne("
            SELECT fk_KELIONĖ
            FROM užsakymai
            WHERE užsakymo_numeris = ?
        ", [$id]);

        if ($tripIdResult) {
            $tripId = $tripIdResult->fk_KELIONĖ;

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

            if (is_array($request->input('country'))) {
                foreach ($request->input('country') as $index => $country) {
                    $routePointId = $validatedData['route_point_id'][$index] ?? null;

                    if ($routePointId) {
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

        return redirect()->route('places');
    }

    public function destroy($id)
    {
        $place = DB::table('užsakymai')->where('užsakymo_numeris', $id)->first();

        if ($place) {
            DB::delete("
                DELETE lv
                FROM lankytinos_vietos lv
                JOIN maršruto_taškai mt ON lv.fk_MARŠRUTO_TAŠKAS = mt.id
                WHERE mt.fk_KELIONĖ = ?
            ", [$place->fk_KELIONĖ]);

            DB::delete("
                DELETE FROM maršruto_taškai
                WHERE fk_KELIONĖ = ?
            ", [$place->fk_KELIONĖ]);

            DB::delete("
                DELETE FROM kelionės
                WHERE id = ?
            ", [$place->fk_KELIONĖ]);

            DB::delete("
                DELETE FROM užsakymai
                WHERE užsakymo_numeris = ?
            ", [$id]);

            return redirect()->route('places');
        }

        return redirect()->route('places');
    }

    public function createPlace(Request $request)
    {
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

        DB::insert("
        INSERT INTO kelionės (būsena, pradžia, pabaiga)
        VALUES (?, ?, ?)
    ", [
            $validatedData['trip_status'],
            $validatedData['trip_start_date'],
            $validatedData['trip_end_date'],
        ]);

        $tripId = DB::getPdo()->lastInsertId();

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

        if (is_array($request->input('country'))) {
            foreach ($request->input('country') as $index => $country) {
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

                $routePointId = DB::getPdo()->lastInsertId();

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

        return redirect()->route('places');
    }

    public function showCreatePlace(Request $request)
    {
        return view('tables.newEntry');
    }

    public function filters(Request $request)
    {
        return view('tables.filters');
    }

    public function aggregates(Request $request)
    {
        $filters = $request->except('_token');

        $baseSql = "
            SELECT
                CONCAT(k.vardas, ' ', k.pavardė) AS PersonName,
                u.užsakymo_numeris AS OrderID,
                u.pasirašymo_data AS BeginOrderDate,
                u.nutraukimo_data AS EndOrderDate,
                COALESCE(COUNT(DISTINCT u.užsakymo_numeris), 0) AS OrderCount,
                SUM(IF(m.suma >= 0, m.suma, 0)) AS OrderPaymentSum,
                ROUND(COALESCE(AVG(m.suma), 0), 1) AS AverageOrderPayment,
                COALESCE(MAX(s.suma), 0) AS MaxOrderBillSum
            FROM
                klientai k
            LEFT JOIN
                užsakymai u ON u.fk_KLIENTAS = k.asmens_kodas
            LEFT JOIN
                sąskaitos s ON s.fk_UŽSAKYMAS = u.užsakymo_numeris
            LEFT JOIN
                mokėjimai m ON m.fk_SĄSKAITA = s.numeris
            :where_clause:
            GROUP BY
                PersonName,
                OrderID ASC,
                BeginOrderDate,
                EndOrderDate
            WITH ROLLUP;
        ";

        $whereClauses = [];
        $bindings = [];

        if (isset($filters['beginDate']))
        {
            $whereClauses[] = "u.pasirašymo_data >= ?";
            $bindings[] = $filters['beginDate'];
        }

        if (isset($filters['endDate']))
        {
            $whereClauses[] = "u.pasirašymo_data < ?";
            $bindings[] = Carbon::make($filters['endDate'])->addDay()->toDateTimeString();
        }

        if (isset($filters['beginEndDate']))
        {
            $whereClauses[] = "u.nutraukimo_data >= ?";
            $bindings[] = $filters['beginDate'];
        }

        if (isset($filters['endEndDate']))
        {
            $whereClauses[] = "u.nutraukimo_data < ?";
            $bindings[] = Carbon::make($filters['endEndDate'])->addDay()->toDateTimeString();
        }

        if (isset($filters['maxSum']))
        {
            $whereClauses[] = "s.suma <= ?";
            $bindings[] = $filters['maxSum'];
        }

        $whereSql = "";

        if (!empty($whereClauses)) {
            $whereSql = "WHERE " . implode(" AND ", $whereClauses);
        }

        $finalSql = str_replace(':where_clause:', $whereSql, $baseSql);

        $aggregatedData = DB::select($finalSql, $bindings);

        return view('tables.aggregates', [
            'aggregatedData' => Collection::make($aggregatedData)->groupBy(['PersonName', 'OrderID'])
        ]);
    }
}
