@extends('layouts/layout')
@section('body')
    <div class="container py-5">
        <div class="d-flex justify-content-end align-items-center mb-4">
            <a href="{{ route('home') }}" class="btn btn-outline-primary">Grįžti į lentelių sąrašą</a>
        </div>
        <table class="table custom-table text-center">
            <thead>
            <tr>
                <th>Darbo laikas</th>
                <th>Įėjimo mokestis</th>
                <th>Reitingas</th>
                <th>Tipas</th>
                <th>Valstybė</th>
                <th>Miestas</th>
                <th>Taško pavadinimas</th>
                <th>Adresas</th>
            </tr>
            </thead>
            <tbody>
            @foreach($places as $place)
                <tr>
                    <td>{{ $place->darbo_laikas }}</td>
                    <td>{{ $place->įėjimo_mokestis }}</td>
                    <td>{{ $place->reitingas }}</td>
                    <td>{{ $place->tipas }}</td>
                    <td>{{ $place->valstybė }}</td>
                    <td>{{ $place->miestas }}</td>
                    <td>{{ $place->taško_pavadinimas }}</td>
                    <td>{{ $place->adresas }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
