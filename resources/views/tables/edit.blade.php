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
            @method('POST')
            @csrf

            {{-- MARŠRUTO TAŠKAI --}}
            <div class="card pastel-card pastel-bg-1 mb-4">
                <h4 class="mb-3">Maršruto Taškai</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Valstybė</label>
                        <input type="text" name="valstybė" value="{{ $place->valstybė ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Miestas</label>
                        <input type="text" name="miestas" value="{{ $place->miestas ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pavadinimas</label>
                        <input type="text" name="pavadinimas" value="{{ $place->pavadinimas ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Adresas</label>
                        <input type="text" name="adresas" value="{{ $place->adresas ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Trukmė</label>
                        <input type="number" name="trukmė" value="{{ $place->trukmė ?? '' }}" class="form-control">
                    </div>
                </div>
            </div>

            {{-- LANKYTINOS VIETOS --}}
            <div class="card pastel-card pastel-bg-2 mb-4">
                <h4 class="mb-3">Lankytinos Vietos</h4>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Darbo laikas</label>
                        <input type="text" name="darbo_laikas" value="{{ $place->darbo_laikas ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Įėjimo mokestis</label>
                        <input type="number" name="įėjimo_mokestis" value="{{ $place->įėjimo_mokestis ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Reitingas</label>
                        <input type="number" name="reitingas" value="{{ $place->reitingas ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipas</label>
                        <select name="tipas" class="form-select">
                            @php
                                $tipai = ['SPA_centras', 'parkas', 'baseinas', 'restoranas', 'kurortas', 'pramogu_centras', 'muziejus'];
                            @endphp
                            @foreach ($tipai as $tipas)
                                <option value="{{ $tipas }}" {{ (isset($place->tipas) && $place->tipas === $tipas) ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $tipas)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- KELIONĖS IR UŽSAKYMAI --}}
            <div class="card pastel-card pastel-bg-3 mb-4">
                <h4 class="mb-3">Kelionės ir Užsakymai</h4>
                <div id="combined-section" class="row g-4">
                    @if(!empty($combinedEntries))
                        @foreach($combinedEntries as $index => $entry)
                            <div class="border-top pt-3">
                                <input type="hidden" name="combined[{{ $index }}][id]" value="{{ $entry->id }}">
                                <div class="row g-3">
                                    @php
                                        $fields = [
                                            'pradžia' => 'Pradžia',
                                            'pabaiga' => 'Pabaiga',
                                            'būsena' => 'Būsena',
                                            'trukmė' => 'Trukmė',
                                            'žmonių_sk' => 'Žmonių Skaičius',
                                            'nutraukimo_data' => 'Nutraukimo Data'
                                        ];
                                    @endphp

                                    @foreach($fields as $field => $label)
                                        <div class="col-md-4">
                                            <label class="form-label">{{ $label }}</label>
                                            @if($field === 'būsena')
                                                <select name="combined[{{ $index }}][{{ $field }}]" class="form-select">
                                                    <option value="planuojama" {{ $entry->$field === 'planuojama' ? 'selected' : '' }}>Planuojama</option>
                                                    <option value="vykdoma" {{ $entry->$field === 'vykdoma' ? 'selected' : '' }}>Vykdoma</option>
                                                    <option value="baigta" {{ $entry->$field === 'baigta' ? 'selected' : '' }}>Baigta</option>
                                                </select>
                                            @else
                                                <input
                                                    type="{{ in_array($field, ['pradžia', 'pabaiga', 'nutraukimo_data']) ? 'datetime-local' : 'number' }}"
                                                    name="combined[{{ $index }}][{{ $field }}]"
                                                    value="{{ $entry->$field }}"
                                                    class="form-control"
                                                >
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" id="add-combined-entry" class="btn btn-outline-primary mt-3">
                    <i class="bi bi-plus-circle me-2"></i>Pridėti Kelionę ir Užsakymą
                </button>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success btn-lg px-5">Išsaugoti</button>
            </div>
        </form>
    </div>

    <script>
        let combinedIndex = {{ count($combinedEntries ?? []) }};

        document.getElementById('add-combined-entry').addEventListener('click', () => {
            const section = document.getElementById('combined-section');
            const div = document.createElement('div');
            div.classList.add('border-top', 'pt-3');

            div.innerHTML = `
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Pradžia</label>
                    <input type="date" name="combined[${combinedIndex}][pradžia]" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pabaiga</label>
                    <input type="date" name="combined[${combinedIndex}][pabaiga]" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Būsena</label>
                    <select name="combined[${combinedIndex}][būsena]" class="form-select">
                        <option value="planuojama">Planuojama</option>
                        <option value="vykdoma">Vykdoma</option>
                        <option value="baigta">Baigta</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Trukmė</label>
                    <input type="number" name="combined[${combinedIndex}][trukmė]" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Žmonių Skaičius</label>
                    <input type="number" name="combined[${combinedIndex}][žmonių_sk]" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nutraukimo Data</label>
                    <input type="date" name="combined[${combinedIndex}][nutraukimo_data]" class="form-control">
                </div>
            </div>
        `;
            section.appendChild(div);
            combinedIndex++;
        });
    </script>
@endsection
