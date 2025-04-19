@extends('layouts/layout')
@section('body')
    <div class="container py-5">
        <div class="d-flex justify-content-end align-items-center mb-4">
            <a href="{{ route('places') }}" class="btn btn-outline-primary">Grįžti į sąrašą</a>
        </div>

        <h1 class="mb-4">Redaguoti Įrašą</h1>

        <!-- Edit Form -->
        <form action="{{ route('places.edit', $place->užsakymo_numeris) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Form fields -->
            <div class="form-group mb-3">
                <label for="start_date">Pasirašymo data</label>
                <input type="date" class="form-control" id="start_date" name="start_date"
                       value="{{ old('start_date', $place->pasirašymo_data ?? '') }}" required>
            </div>

            <div class="form-group mb-3">
                <label for="end_date">Nutraukimo data</label>
                <input type="date" class="form-control" id="end_date" name="end_date"
                       value="{{ old('end_date', $place->nutraukimo_data ?? '') }}" required>
            </div>

            <div class="form-group mb-3">
                <label for="duration">Trukmė (dienos)</label>
                <input type="number" class="form-control" id="duration" name="duration"
                       value="{{ old('duration', $place->trukmė ?? '') }}" required>
            </div>

            <div class="form-group mb-3">
                <label for="people_count">Žmonių Skaičius</label>
                <input type="number" class="form-control" id="people_count" name="people_count"
                       value="{{ old('people_count', $place->žmonių_sk ?? '') }}" required>
            </div>

            <div class="form-group mb-3">
                <label for="trip_start_date">Kelionės pradžia</label>
                <input type="date" class="form-control" id="trip_start_date" name="trip_start_date"
                       value="{{ old('trip_start_date', $place->pradžia ?? '') }}" required>
            </div>

            <div class="form-group mb-3">
                <label for="trip_end_date">Kelionės pabaiga</label>
                <input type="date" class="form-control" id="trip_end_date" name="trip_end_date"
                       value="{{ old('trip_end_date', $place->pabaiga ?? '') }}" required>
            </div>

            <div class="form-group mb-3">
                <label for="trip_status">Būsena</label>
                <select class="form-control" id="trip_status" name="trip_status" required>
                    <option value="vykdoma" {{ old('trip_status', $place->būsena ?? '') == 'vykdoma' ? 'selected' : '' }}>Vykdoma</option>
                    <option value="planuojama" {{ old('trip_status', $place->būsena ?? '') == 'planuojama' ? 'selected' : '' }}>Planuojama</option>
                    <option value="baigta" {{ old('trip_status', $place->būsena ?? '') == 'baigta' ? 'selected' : '' }}>Baigta</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Išsaugoti</button>
        </form>
    </div>
@endsection
