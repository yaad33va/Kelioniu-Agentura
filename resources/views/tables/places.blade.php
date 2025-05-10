@extends('layouts/layout')

@section('body')
    <div class="container py-5">
        <div class="d-flex justify-content-end align-items-center mb-4">
            <a href="{{ route('home') }}" class="btn btn-outline-primary">Grįžti į lentelių sąrašą</a>
        </div>
        <h1 class="mb-4">Maršrutai</h1>
        <table class="table custom-table text-center">
            <thead>
            <tr>
                <th>Eilės numeris</th>
                <th>Užsakymo suformavimo data</th>
                <th>Užsakymo nutraukimo data</th>
                <th>Trukmė</th>
                <th>Žmonių skaičius</th>
                <th>Kelionės pradžia</th>
                <th>Kelionės pabaiga</th>
                <th>Būsena</th>
                <th>Redaguoti</th>
                <th>Veiksmai</th>
            </tr>
            </thead>
            <tbody>
            @foreach($places as $place)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $place->start_date }}</td>
                    <td>{{ $place->end_date }}</td>
                    <td>{{ $place->duration ?? 'N/A' }}</td>
                    <td>{{ $place->people_count ?? 'N/A' }}</td>
                    <td>{{ $place->trip_start_date }}</td>
                    <td>{{ $place->trip_end_date }}</td>
                    <td>{{ $place->status }}</td>
                    <td>
                        <a href="{{ route('places.edit', $place->record_id) }}" class="btn btn-primary btn-sm">Redaguoti įrašą</a>
                    </td>
                    <td>
                        <form action="{{ route('places.destroy', $place->record_id) }}" method="POST" onsubmit="return confirm('Ar tikrai norite ištrinti šį įrašą?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Ištrinti</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-end mt-4">
            <a href="{{ route('places.createPlace') }}" class="btn btn-success">Pridėti naują įrašą</a>
        </div>
        <div class="d-flex justify-content-end">
            <a href="{{ route('home') }}" class="btn btn-outline-primary">Grįžti</a>
        </div>
    </div>
@endsection
