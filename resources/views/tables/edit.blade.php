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
                       value="{{ old('trip_start_date', \Carbon\Carbon::parse($place->pradžia)->format('Y-m-d') ?? '') }}" required>
            </div>

            <div class="form-group mb-3">
                <label for="trip_end_date">Kelionės pabaiga</label>
                <input type="date" class="form-control" id="trip_end_date" name="trip_end_date"
                       value="{{ old('trip_end_date', $place->pabaiga ? \Carbon\Carbon::parse($place->pabaiga)->format('Y-m-d') : '') }}">
            </div>

            <div class="form-group mb-3">
                <label for="trip_status">Būsena</label>
                <select class="form-control" id="trip_status" name="trip_status" required>
                    <option value="vykdoma" {{ old('trip_status', $place->būsena ?? '') == 'vykdoma' ? 'selected' : '' }}>Vykdoma</option>
                    <option value="planuojama" {{ old('trip_status', $place->būsena ?? '') == 'planuojama' ? 'selected' : '' }}>Planuojama</option>
                    <option value="baigta" {{ old('trip_status', $place->būsena ?? '') == 'baigta' ? 'selected' : '' }}>Baigta</option>
                </select>
            </div>

            <h3 class="mt-4">Maršruto Taškai ir Lankytinos Vietos</h3>
            <div id="dynamic-rows">
                @if(isset($routePoints) && count($routePoints) > 0)
                    @foreach($routePoints as $index => $routePoint)
                        <div class="row align-items-end mb-3 dynamic-row">
                            <div class="col-md-6">
                                <input type="hidden" name="route_point_id[]" value="{{ $routePoint->id }}">
                                <div class="form-group mb-3">
                                    <label for="country[]">Valstybė</label>
                                    <input type="text" class="form-control" name="country[]"
                                           value="{{ old('country.' . $index, $routePoint->valstybė ?? '') }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="city[]">Miestas</label>
                                    <input type="text" class="form-control" name="city[]"
                                           value="{{ old('city.' . $index, $routePoint->miestas ?? '') }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name[]">Pavadinimas</label>
                                    <input type="text" class="form-control" name="name[]"
                                           value="{{ old('name.' . $index, $routePoint->pavadinimas ?? '') }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="address[]">Adresas</label>
                                    <input type="text" class="form-control" name="address[]"
                                           value="{{ old('address.' . $index, $routePoint->adresas ?? '') }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="route_duration[]">Trukmė (valandos)</label>
                                    <input type="number" class="form-control" name="route_duration[]"
                                           value="{{ old('route_duration.' . $index, $routePoint->trukmė ?? '') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                @php
                                    $landmarkForRoutePoint = null;
                                    foreach ($landmarks as $landmark) {
                                        if ($landmark->fk_MARŠRUTO_TAŠKAS == $routePoint->id) {
                                            $landmarkForRoutePoint = $landmark;
                                            break;
                                        }
                                    }
                                @endphp
                                <input type="hidden" name="landmark_id[]" value="{{ $landmarkForRoutePoint->id ?? '' }}">
                                <div class="form-group mb-3">
                                    <label for="working_hours[]">Darbo Laikas</label>
                                    <input type="text" class="form-control" name="working_hours[]"
                                           value="{{ old('working_hours.' . $index, $landmarkForRoutePoint->darbo_laikas ?? '') }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="entry_fee[]">Įėjimo Mokestis</label>
                                    <input type="text" class="form-control" name="entry_fee[]"
                                           value="{{ old('entry_fee.' . $index, $landmarkForRoutePoint->įėjimo_mokestis ?? '') }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="type[]">Tipas</label>
                                    <select class="form-control" name="type[]">
                                        <option value="SPA centras" {{ old('type.' . $index, $landmarkForRoutePoint->tipas ?? '') == 'SPA centras' ? 'selected' : '' }}>SPA centras</option>
                                        <option value="parkas" {{ old('type.' . $index, $landmarkForRoutePoint->tipas ?? '') == 'parkas' ? 'selected' : '' }}>Parkas</option>
                                        <option value="baseinas" {{ old('type.' . $index, $landmarkForRoutePoint->tipas ?? '') == 'baseinas' ? 'selected' : '' }}>Baseinas</option>
                                        <option value="restoranas" {{ old('type.' . $index, $landmarkForRoutePoint->tipas ?? '') == 'restoranas' ? 'selected' : '' }}>Restoranas</option>
                                        <option value="kurortas" {{ old('type.' . $index, $landmarkForRoutePoint->tipas ?? '') == 'kurortas' ? 'selected' : '' }}>Kurortas</option>
                                        <option value="pramogų centras" {{ old('type.' . $index, $landmarkForRoutePoint->tipas ?? '') == 'pramogų centras' ? 'selected' : '' }}>Pramogų centras</option>
                                        <option value="muziejus" {{ old('type.' . $index, $landmarkForRoutePoint->tipas ?? '') == 'muziejus' ? 'selected' : '' }}>Muziejus</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="rating[]">Reitingas</label>
                                    <input type="number" class="form-control" name="rating[]" step="0.1" max="5"
                                           value="{{ old('rating.' . $index, $landmarkForRoutePoint->reitingas ?? '') }}">
                                </div>
                            </div>

                            <div class="col-md-12 text-end">
                                <button type="button" class="btn btn-danger remove-row">Šalinti</button>
                                @if(isset($routePoint->id))
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="delete_route_points[]" value="{{ $routePoint->id }}" id="delete_route_point_{{ $routePoint->id }}">
                                        <label class="form-check-label" for="delete_route_point_{{ $routePoint->id }}">
                                            Pažymėti pašalinimui
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="row align-items-end mb-3 dynamic-row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="country[]">Valstybė</label>
                                <input type="text" class="form-control" name="country[]" value="">
                            </div>
                            <div class="form-group mb-3">
                                <label for="city[]">Miestas</label>
                                <input type="text" class="form-control" name="city[]" value="">
                            </div>
                            <div class="form-group mb-3">
                                <label for="name[]">Pavadinimas</label>
                                <input type="text" class="form-control" name="name[]" value="">
                            </div>
                            <div class="form-group mb-3">
                                <label for="address[]">Adresas</label>
                                <input type="text" class="form-control" name="address[]" value="">
                            </div>
                            <div class="form-group mb-3">
                                <label for="route_duration[]">Trukmė (valandos)</label>
                                <input type="number" class="form-control" name="route_duration[]" value="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="working_hours[]">Darbo Laikas</label>
                                <input type="text" class="form-control" name="working_hours[]" value="">
                            </div>
                            <div class="form-group mb-3">
                                <label for="entry_fee[]">Įėjimo Mokestis</label>
                                <input type="text" class="form-control" name="entry_fee[]" value="">
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
                            <div class="form-group mb-3">
                                <label for="rating[]">Reitingas</label>
                                <input type="number" class="form-control" name="rating[]" step="0.1" max="5" value="">
                            </div>
                        </div>
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-danger remove-row">Šalinti</button>
                        </div>
                    </div>
                @endif
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

            document.getElementById('add-row').addEventListener('click', function () {
                const firstRow = dynamicRowsContainer.querySelector('.dynamic-row');
                if (firstRow) {
                    const newRow = firstRow.cloneNode(true);
                    Array.from(newRow.querySelectorAll('input[type="text"], input[type="number"]')).forEach(input => input.value = '');
                    Array.from(newRow.querySelectorAll('select')).forEach(select => select.selectedIndex = 0);
                    const deleteCheckbox = newRow.querySelector('input[name="delete_route_points[]"]');
                    if (deleteCheckbox) {
                        deleteCheckbox.remove();
                        const labelToRemove = newRow.querySelector('label[for^="delete_route_point_"]');
                        if (labelToRemove) {
                            labelToRemove.remove();
                        }
                    }
                    dynamicRowsContainer.appendChild(newRow);
                } else {
                    const newRowHtml = `
                        <div class="row align-items-end mb-3 dynamic-row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="country[]">Valstybė</label>
                                    <input type="text" class="form-control" name="country[]" value="">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="city[]">Miestas</label>
                                    <input type="text" class="form-control" name="city[]" value="">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name[]">Pavadinimas</label>
                                    <input type="text" class="form-control" name="name[]" value="">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="address[]">Adresas</label>
                                    <input type="text" class="form-control" name="address[]" value="">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="route_duration[]">Trukmė (valandos)</label>
                                    <input type="number" class="form-control" name="route_duration[]" value="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="working_hours[]">Darbo Laikas</label>
                                    <input type="text" class="form-control" name="working_hours[]" value="">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="entry_fee[]">Įėjimo Mokestis</label>
                                    <input type="text" class="form-control" name="entry_fee[]" value="">
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
                                <div class="form-group mb-3">
                                    <label for="rating[]">Reitingas</label>
                                    <input type="number" class="form-control" name="rating[]" step="0.1" max="5"
                                    value="">
                                </div>
                            </div>
                            <div class="col-md-12 text-end">
                                <button type="button" class="btn btn-danger remove-row">Šalinti</button>
                            </div>
                        </div>
                    `;
                    dynamicRowsContainer.insertAdjacentHTML('beforeend', newRowHtml);
                }
            });

            dynamicRowsContainer.addEventListener('click', function (event) {
                if (event.target.classList.contains('remove-row')) {
                    if (document.querySelectorAll('.dynamic-row').length > 1) {
                        event.target.closest('.dynamic-row').remove();
                    }
                }
            });
        });
    </script>
@endsection
