@extends('layouts/layout')
@section('body')
    <div class="container py-5">
        <div class="d-flex justify-content-end align-items-center mb-4">
            <a href="{{ route('places') }}" class="btn btn-outline-primary">Grįžti į sąrašą</a>
        </div>

        <h1 class="mb-4">Redaguoti Įrašą</h1>

        <form action="{{ route('places.update', $place->užsakymo_numeris) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Main form fields -->
            <div class="form-group mb-3">
                <label for="start_date">Pasirašymo data</label>
                <input type="date" class="form-control" id="start_date" name="start_date"
                       value="{{ old('start_date', $place->pasirašymo_data ?? '') }}" required>
            </div>

            <div class="form-group mb-3">
                <label for="end_date">Nutraukimo data</label>
                <input type="date" class="form-control" id="end_date" name="end_date"
                       value="{{ old('end_date', $place->nutraukimo_data ?? '') }}">
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
                       value="{{ old('trip_end_date', $place->pabaiga ?? '') }}">
            </div>

            <div class="form-group mb-3">
                <label for="trip_status">Būsena</label>
                <select class="form-control" id="trip_status" name="trip_status" required>
                    <option value="vykdoma" {{ old('trip_status', $place->būsena) == 'vykdoma' ? 'selected' : '' }}>Vykdoma</option>
                    <option value="planuojama" {{ old('trip_status', $place->būsena) == 'planuojama' ? 'selected' : '' }}>Planuojama</option>
                    <option value="baigta" {{ old('trip_status', $place->būsena) == 'baigta' ? 'selected' : '' }}>Baigta</option>
                </select>
            </div>

            <h3 class="mt-4">Maršruto Taškai ir Lankytinos Vietos</h3>
            <div id="dynamic-rows">
                @foreach($routePoints as $index => $point)
                    <div class="dynamic-row border rounded p-3 mb-3">
                        <input type="hidden" name="route_point_id[]" value="{{ $point->id }}">
                        <input type="hidden" name="landmark_id[]" value="{{ $landmarks[$index]->id ?? '' }}">

                        <div class="form-group mb-2">
                            <label>Valstybė</label>
                            <input type="text" name="country[]" class="form-control" value="{{ $point->valstybė }}">
                        </div>

                        <div class="form-group mb-2">
                            <label>Miestas</label>
                            <input type="text" name="city[]" class="form-control" value="{{ $point->miestas }}">
                        </div>

                        <div class="form-group mb-2">
                            <label>Pavadinimas</label>
                            <input type="text" name="name[]" class="form-control" value="{{ $point->pavadinimas }}">
                        </div>

                        <div class="form-group mb-2">
                            <label>Adresas</label>
                            <input type="text" name="address[]" class="form-control" value="{{ $point->adresas }}">
                        </div>

                        <div class="form-group mb-2">
                            <label>Kelionės rukmė</label>
                            <input type="number" name="route_duration[]" class="form-control" value="{{ $point->trukmė }}">
                        </div>

                        <div class="form-group mb-2">
                            <label>Darbo laikas</label>
                            <input type="text" name="working_hours[]" class="form-control" value="{{ $landmarks[$index]->darbo_laikas ?? '' }}">
                        </div>

                        <div class="form-group mb-2">
                            <label>Įėjimo mokestis</label>
                            <input type="number" step="0.01" name="entry_fee[]" class="form-control" value="{{ $landmarks[$index]->įėjimo_mokestis ?? '' }}">
                        </div>

                        <div class="form-group mb-3">
                            <label for="type[]">Tipas</label>
                            <select class="form-control" name="type[]">
                                <option value="SPA centras">SPA centras</option>
                                <option value="parkas">Parkas</option>
                                <option value="baseinas">Baseinas</option>
                                <option value="restoranas">Restoranas</option>
                                <option value="kurortas">Kurortas</option>
                                <option value="pramogų centras">Pramogų centras</option>
                                <option value="muziejus">Muziejus</option>
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label>Reitingas</label>
                            <input type="number" name="rating[]" class="form-control" min="0" max="5" value="{{ $landmarks[$index]->reitingas ?? '' }}">
                        </div>

                        <div class="form-check mb-2">
                            <input type="checkbox" name="delete_route_points[]" class="form-check-input" value="{{ $point->id }}">
                            <label class="form-check-label">Pašalinti šį tašką</label>
                        </div>

                        <button type="button" class="btn btn-danger btn-sm remove-row">Pažymėti šalinimui</button>
                    </div>
                @endforeach
            </div>

            <div class="text-end mb-3">
                <button type="button" id="add-row" class="btn btn-success">Pridėti naują</button>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Išsaugoti</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dynamicRowsContainer = document.getElementById('dynamic-rows');
            const addRowButton = document.getElementById('add-row');

            addRowButton.addEventListener('click', function () {
                const rows = dynamicRowsContainer.querySelectorAll('.dynamic-row');
                if (rows.length === 0) return;

                const lastRow = rows[rows.length - 1];
                const newRow = lastRow.cloneNode(true);

                // Reset all inputs inside the cloned row
                newRow.querySelectorAll('input, select').forEach(input => {
                    // Remove values
                    if (input.name === 'route_point_id[]' || input.name === 'landmark_id[]') {
                        input.remove(); // remove hidden identifiers
                    } else if (input.type === 'checkbox') {
                        input.checked = false;
                    } else {
                        input.value = '';
                    }
                });

                // Reset row style
                newRow.style.opacity = '1';

                dynamicRowsContainer.appendChild(newRow);
            });

            dynamicRowsContainer.addEventListener('click', function (event) {
                if (event.target.classList.contains('remove-row')) {
                    const row = event.target.closest('.dynamic-row');
                    const deleteCheckbox = row.querySelector('input[name="delete_route_points[]"]');
                    if (deleteCheckbox) {
                        deleteCheckbox.checked = true;
                        row.style.opacity = 0.5;
                    } else {
                        // For newly added rows
                        row.remove();
                    }
                }
            });
        });
    </script>
@endsection
