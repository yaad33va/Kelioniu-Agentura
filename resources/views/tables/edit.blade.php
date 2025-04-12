@extends('layouts/layout')
@section('body')
    <div class="container py-5">
        <h2 class="mb-4">Redaguoti Įrašą</h2>
        <form action="{{ route('places.update', $place->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Input fields for MARŠRUTO_TAŠKAI -->
            <div class="mb-3">
                <label for="valstybe" class="form-label">Valstybė</label>
                <input type="text" class="form-control" id="valstybe" name="valstybė" value="{{ $place->valstybė }}" required>
            </div>
            <div class="mb-3">
                <label for="miestas" class="form-label">Miestas</label>
                <input type="text" class="form-control" id="miestas" name="miestas" value="{{ $place->miestas }}" required>
            </div>
            <div class="mb-3">
                <label for="pavadinimas" class="form-label">Pavadinimas</label>
                <input type="text" class="form-control" id="pavadinimas" name="pavadinimas" value="{{ $place->pavadinimas }}" required>
            </div>
            <div class="mb-3">
                <label for="adresas" class="form-label">Adresas</label>
                <input type="text" class="form-control" id="adresas" name="adresas" value="{{ $place->adresas }}" required>
            </div>
            <div class="mb-3">
                <label for="trukme" class="form-label">Trukmė</label>
                <input type="number" class="form-control" id="trukme" name="trukmė" value="{{ $place->trukmė }}" required>
            </div>

            <!-- Input fields for LANKYTINOS_VIETOS -->
            <div class="mb-3">
                <label for="darbo_laikas" class="form-label">Darbo laikas</label>
                <input type="text" class="form-control" id="darbo_laikas" name="darbo_laikas" value="{{ $place->darbo_laikas }}" required>
            </div>
            <div class="mb-3">
                <label for="iejimo_mokestis" class="form-label">Įėjimo mokestis</label>
                <input type="number" class="form-control" id="iejimo_mokestis" name="įėjimo_mokestis" value="{{ $place->įėjimo_mokestis }}" required>
            </div>
            <div class="mb-3">
                <label for="reitingas" class="form-label">Reitingas</label>
                <input type="number" class="form-control" id="reitingas" name="reitingas" value="{{ $place->reitingas }}" required>
            </div>
            <div class="mb-3">
                <label for="tipas" class="form-label">Tipas</label>
                <select class="form-select" id="tipas" name="tipas" required>
                    <option value="SPA_centras" {{ $place->tipas == 'SPA_centras' ? 'selected' : '' }}>SPA Centras</option>
                    <option value="parkas" {{ $place->tipas == 'parkas' ? 'selected' : '' }}>Parkas</option>
                    <option value="baseinas" {{ $place->tipas == 'baseinas' ? 'selected' : '' }}>Baseinas</option>
                    <option value="restoranas" {{ $place->tipas == 'restoranas' ? 'selected' : '' }}>Restoranas</option>
                    <option value="kurortas" {{ $place->tipas == 'kurortas' ? 'selected' : '' }}>Kurortas</option>
                    <option value="pramogu_centras" {{ $place->tipas == 'pramogu_centras' ? 'selected' : '' }}>Pramogų Centras</option>
                    <option value="muziejus" {{ $place->tipas == 'muziejus' ? 'selected' : '' }}>Muziejus</option>
                </select>
            </div>

            <input type="hidden" name="fk_MARŠRUTO_TAŠKAS" value="{{ $place->fk_MARŠRUTO_TAŠKAS }}">

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Atnaujinti</button>
            <a href="{{ route('places') }}" class="btn btn-secondary">Atšaukti</a>
        </form>
    </div>
@endsection
