@extends('layouts/layout')
@section('body')
    <div class="container py-5">
        <div class="d-flex justify-content-end align-items-center mb-4">
            <a href="{{ route('home') }}" class="btn btn-outline-primary">Grįžti į lentelių sąrašą</a>
        </div>
        <table class="table custom-table text-center">
            <thead>
            <tr>
                <th>Įrašo numeris</th>
                <th>Valstybė</th>
                <th>Miestas</th>
                <th>Taško pavadinimas</th>
                <th>Adresas</th>
                <th>Darbo laikas</th>
                <th>Įėjimo mokestis</th>
                <th>Reitingas</th>
                <th>Tipas</th>
                <th>Ištrinti</th>
                <th>Redaguoti</th>
            </tr>
            </thead>
            <tbody>
            @foreach($places as $place)
                <tr>
                    <td>{{ array_search($place, $places) + 1 }}</td>
                    <td>{{ $place->valstybė }}</td>
                    <td>{{ $place->miestas }}</td>
                    <td>{{ $place->taško_pavadinimas }}</td>
                    <td>{{ $place->adresas }}</td>
                    <td>{{ $place->darbo_laikas }}</td>
                    <td>{{ $place->įėjimo_mokestis }}</td>
                    <td>{{ $place->reitingas }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $place->tipas)) }}</td>
                    <td>
                        <form action="{{ route('places.destroy', $place->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Ištrinti įrašą</button>
                        </form>
                    </td>
                    <td>
                        <form action="{{ route('places.showPlace', $place->id) }}" method="GET">
                            @csrf
                            @method('GET')
                            <button type="submit" class="btn btn-primary btn-sm">Redaguoti įrašą</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <form action="{{ route('places.createPlace') }}" method="GET">
            @csrf
            @method('GET')
            <button type="submit" class="btn btn-outline-primary btn-sm">Pridėti įrašus</button>
        </form>
    </div>
@endsection
