@extends('layouts/layout')

@section('body')
    <div class="container my-5">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Klaida!</strong>
                <ul class="mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h1 class="mb-4 fw-bold">Valdyti Įrašus</h1>

        <form action="{{ route('places.update', $place->lv_id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- MARŠRUTO TAŠKAI --}}
            <div class="card pastel-card pastel-bg-1 mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-3">Maršruto Taškai</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="valstybė" class="form-label">Valstybė</label>
                            <input type="text" name="valstybė" id="valstybė"
                                   value="{{ old('valstybė', $place->valstybė ?? '') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="miestas" class="form-label">Miestas</label>
                            <input type="text" name="miestas" id="miestas"
                                   value="{{ old('miestas', $place->miestas ?? '') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="pavadinimas" class="form-label">Pavadinimas</label>
                            <input type="text" name="pavadinimas" id="pavadinimas"
                                   value="{{ old('pavadinimas', $place->pavadinimas ?? '') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="adresas" class="form-label">Adresas</label>
                            <input type="text" name="adresas" id="adresas"
                                   value="{{ old('adresas', $place->adresas ?? '') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="trukmė" class="form-label">Trukmė</label>
                            <input type="number" name="trukmė" id="trukmė"
                                   value="{{ old('trukmė', $place->trukmė ?? '') }}" class="form-control">
                        </div>
                    </div>
                    <input type="hidden" name="fk_MARŠRUTO_TAŠKAS" value="{{ $place->fk_MARŠRUTO_TAŠKAS }}">
                </div>
            </div>

            {{-- LANKYTINOS VIETOS --}}
            <div class="card pastel-card pastel-bg-2 mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-3">Lankytinos Vietos</h4>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="darbo_laikas" class="form-label">Darbo laikas</label>
                            <input type="text" name="darbo_laikas" id="darbo_laikas"
                                   value="{{ old('darbo_laikas', $place->darbo_laikas ?? '') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="įėjimo_mokestis" class="form-label">Įėjimo mokestis</label>
                            <input type="number" name="įėjimo_mokestis" id="įėjimo_mokestis"
                                   value="{{ old('įėjimo_mokestis', $place->įėjimo_mokestis ?? '') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="reitingas" class="form-label">Reitingas</label>
                            <input type="number" name="reitingas" id="reitingas"
                                   value="{{ old('reitingas', $place->reitingas ?? '') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="tipas" class="form-label">Tipas</label>
                            <select name="tipas" id="tipas" class="form-select">
                                @php
                                    $tipai = ['SPA_centras', 'parkas', 'baseinas', 'restoranas', 'kurortas', 'pramogu_centras', 'muziejus'];
                                @endphp
                                @foreach ($tipai as $tipas)
                                    <option value="{{ $tipas }}"
                                        {{ old('tipas', $place->tipas ?? '') === $tipas ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $tipas)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KELIONĖS ir UŽSAKYMAI --}}
            <div class="card pastel-card pastel-bg-4 mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-3">Kelionės ir Užsakymai</h4>
                    <div id="keliones-uzsakymai-section" class="row g-4">
                        @if (!empty($keliones))
                            @foreach ($keliones as $index => $kelione)
                                <div class="border-top pt-3 kelione-container">
                                    <input type="hidden" name="keliones[{{ $index }}][id]" value="{{ $kelione->id ?? '' }}">
                                    <input type="hidden" name="keliones[{{ $index }}][method]" value="update" />
                                    <div class="row g-3 mb-3 align-items-center">
                                        <div class="col-md-4">
                                            <label for="pradžia-{{ $index }}" class="form-label">Pradžia</label>
                                            <input type="date" name="keliones[{{ $index }}][pradžia]"
                                                   id="pradžia-{{ $index }}"
                                                   value="{{ old('keliones.' . $index . '.pradžia', $kelione->pradžia ?? '') }}"
                                                   class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="pabaiga-{{ $index }}" class="form-label">Pabaiga</label>
                                            <input type="date" name="keliones[{{ $index }}][pabaiga]"
                                                   id="pabaiga-{{ $index }}"
                                                   value="{{ old('keliones.' . $index . '.pabaiga', $kelione->pabaiga ?? '') }}"
                                                   class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="būsena-{{ $index }}" class="form-label">Būsena</label>
                                            <select name="keliones[{{ $index }}][būsena]"
                                                    id="būsena-{{ $index }}" class="form-select">
                                                <option value="planuojama"
                                                    {{ old('keliones.' . $index . '.būsena', $kelione->būsena ?? '') === 'planuojama' ? 'selected' : '' }}>
                                                    Planuojama</option>
                                                <option value="vykdoma"
                                                    {{ old('keliones.' . $index . '.būsena', $kelione->būsena ?? '') === 'vykdoma' ? 'selected' : '' }}>
                                                    Vykdoma</option>
                                                <option value="baigta"
                                                    {{ old('keliones.' . $index . '.būsena', $kelione->būsena ?? '') === 'baigta' ? 'selected' : '' }}>
                                                    Baigta</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-3 align-items-center">
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-danger btn-sm delete-kelione-btn"
                                                    data-kelione-index="{{ $index }}">Ištrinti kelionę</button>
                                        </div>
                                    </div>
                                    <h5 class="mb-2">Užsakymai:</h5>
                                    <div id="uzsakymai-section-{{ $index }}" class="row g-4">
                                        @if (!empty($kelione->užsakymai))
                                            @foreach ($kelione->užsakymai as $uzsakymoIndex => $uzsakymas)
                                                <div class="border-top pt-3 uzsakymas-container">
                                                    <input type="hidden"
                                                           name="keliones[{{ $index }}][užsakymai][{{ $uzsakymoIndex }}][id]"
                                                           value="{{ $uzsakymas->užsakymo_numeris ?? '' }}">
                                                    <input type="hidden" name="keliones[{{ $index }}][užsakymai][{{ $uzsakymoIndex }}][method]" value="update" />
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label for="trukmė-{{ $index }}-{{ $uzsakymoIndex }}"
                                                                   class="form-label">Trukmė</label>
                                                            <input type="number"
                                                                   name="keliones[{{ $index }}][užsakymai][{{ $uzsakymoIndex }}][trukmė]"
                                                                   id="trukmė-{{ $index }}-{{ $uzsakymoIndex }}"
                                                                   class="form-control"
                                                                   value="{{ old('keliones.' . $index . '.užsakymai.' . $uzsakymoIndex . '.trukmė', $uzsakymas->trukmė ?? '') }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="žmonių_sk-{{ $index }}-{{ $uzsakymoIndex }}"
                                                                   class="form-label">Žmonių Skaičius</label>
                                                            <input type="number"
                                                                   name="keliones[{{ $index }}][užsakymai][{{ $uzsakymoIndex }}][žmonių_sk]"
                                                                   id="žmonių_sk-{{ $index }}-{{ $uzsakymoIndex }}"
                                                                   class="form-control"
                                                                   value="{{ old('keliones.' . $index . '.užsakymai.' . $uzsakymoIndex . '.žmonių_sk', $uzsakymas->žmonių_sk ?? '') }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="nutraukimo_data-{{ $index }}-{{ $uzsakymoIndex }}"
                                                                   class="form-label">Nutraukimo Data</label>
                                                            <input type="date"
                                                                   name="keliones[{{ $index }}][užsakymai][{{ $uzsakymoIndex }}][nutraukimo_data]"
                                                                   id="nutraukimo_data-{{ $index }}-{{ $uzsakymoIndex }}"
                                                                   class="form-control"
                                                                   value="{{ old('keliones.' . $index . '.užsakymai.' . $uzsakymoIndex . '.nutraukimo_data', $uzsakymas->nutraukimo_data ?? '') }}">
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                            class="btn btn-danger btn-sm mt-2 delete-uzsakymas-btn"
                                                            data-kelione-index="{{ $index }}"
                                                            data-uzsakymas-index="{{ $uzsakymoIndex }}">Ištrinti Užsakymą</button>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-outline-primary mt-3 add-uzsakymas-btn"
                                            data-kelione-index="{{ $index }}">
                                        <i class="bi bi-plus-circle me-2"></i>Pridėti Užsakymą
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-kelione" class="btn btn-outline-primary mt-3">
                        <i class="bi bi-plus-circle me-2"></i>Pridėti Kelionę
                    </button>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success btn-lg px-5">Išsaugoti</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let kelioneIndex = {{ count($keliones ?? []) }};

            document.getElementById('add-kelione').addEventListener('click', () => {
                const section = document.getElementById('keliones-uzsakymai-section');
                const newKelioneIndex = kelioneIndex;
                const div = document.createElement('div');
                div.className = 'border-top pt-3 kelione-container';
                div.innerHTML = `
                    <input type="hidden" name="keliones[${newKelioneIndex}][id]" value="">
                    <input type="hidden" name="keliones[${newKelioneIndex}][method]" value="new" />
                    <div class="row g-3 mb-3 align-items-center">
                        <div class="col-md-4">
                            <label for="pradžia-${newKelioneIndex}" class="form-label">Pradžia</label>
                            <input type="date" name="keliones[${newKelioneIndex}][pradžia]" id="pradžia-${newKelioneIndex}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="pabaiga-${newKelioneIndex}" class="form-label">Pabaiga</label>
                            <input type="date" name="keliones[${newKelioneIndex}][pabaiga]" id="pabaiga-${newKelioneIndex}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="būsena-${newKelioneIndex}" class="form-label">Būsena</label>
                            <select name="keliones[${newKelioneIndex}][būsena]" id="būsena-${newKelioneIndex}" class="form-select">
                                <option value="planuojama">Planuojama</option>
                                <option value="vykdoma">Vykdoma</option>
                                <option value="baigta">Baigta</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3 align-items-center">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger btn-sm delete-kelione-btn" data-kelione-index="${newKelioneIndex}">Ištrinti kelionę</button>
                        </div>
                    </div>
                    <h5 class="mb-2">Užsakymai:</h5>
                    <div id="uzsakymai-section-${newKelioneIndex}" class="row g-4">
                    </div>
                    <button type="button" class="btn btn-outline-primary mt-3 add-uzsakymas-btn" data-kelione-index="${newKelioneIndex}">
                        <i class="bi bi-plus-circle me-2"></i>Pridėti Užsakymą
                    </button>
                `;
                section.appendChild(div);

                kelioneIndex++;
            });

            $(document).on('click', '.add-uzsakymas-btn', function() {
                const kelioneIndex = $(this).data('kelione-index');
                const section = document.getElementById(`uzsakymai-section-${kelioneIndex}`);
                const uzsakymasIndex = section.querySelectorAll('.uzsakymas-container').length;
                const div = document.createElement('div');
                div.className = 'border-top pt-3 uzsakymas-container';
                div.innerHTML = `
                    <input type="hidden" name="keliones[${kelioneIndex}][užsakymai][${uzsakymasIndex}][id]" value="">
                    <input type="hidden" name="keliones[${kelioneIndex}][užsakymai][${uzsakymasIndex}][method]" value="new" />
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="trukmė-${kelioneIndex}-${uzsakymasIndex}" class="form-label">Trukmė</label>
                            <input type="number" name="keliones[${kelioneIndex}][užsakymai][${uzsakymasIndex}][trukmė]" id="trukmė-${kelioneIndex}-${uzsakymasIndex}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="žmonių_sk-${kelioneIndex}-${uzsakymasIndex}" class="form-label">Žmonių Skaičius</label>
                            <input type="number" name="keliones[${kelioneIndex}][užsakymai][${uzsakymasIndex}][žmonių_sk]" id="žmonių_sk-${kelioneIndex}-${uzsakymasIndex}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="nutraukimo_data-${kelioneIndex}-${uzsakymasIndex}" class="form-label">Nutraukimo Data</label>
                            <input type="date" name="keliones[${kelioneIndex}][užsakymai][${uzsakymasIndex}][nutraukimo_data]" id="nutraukimo_data-${kelioneIndex}-${uzsakymasIndex}" class="form-control">
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm mt-2 delete-uzsakymas-btn" data-kelione-index="${kelioneIndex}" data-uzsakymas-index="${uzsakymasIndex}">Ištrinti Užsakymą</button>
                `;
                section.appendChild(div);
            });

            $(document).on('click', '.delete-uzsakymas-btn', function() {
                $(this).closest('.uzsakymas-container').hide();
                $(this).closest('.uzsakymas-container').children()[1] .value = 'delete';

            });

            $(document).on('click', '.delete-kelione-btn', function() {
                $(this).closest('.kelione-container').hide();
                $(this).closest('.kelione-container').children()[1] .value = 'delete';
            });

        });
    </script>
@endsection
