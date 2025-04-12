@extends('layouts/layout')
@section('body')

<div class="container pb-5 px-3 px-md-5">
    @php
        $descriptions = [
            'keliones' => 'Peržiūrėti kiekvienos kelionės informaciją, jos pradžią, pabaigą ir būseną.',
            'lankytinos_vietos' => 'Lankytinų vietų sąrašas.',
            'maršruto_taškai' => 'Peržiūrėti maršrutų taškų sąrašą ir informaciją.',
            'užsakymai' => 'Peržiūrėti klientų užsakymų informaciją.',
            'viešbučiai' => 'Peržiūrėti viešbučių duomenis ir kontaktus.'
        ];

        $icons = [
            'keliones' => 'bi bi-bus-front-fill',
            'lankytinos_vietos' => 'bi bi-geo-alt-fill',
            'maršruto_taškai' => 'bi bi-signpost-2-fill',
            'užsakymai' => 'bi bi-file-earmark-text-fill',
            'viešbučiai' => 'bi bi-building-check'
        ];
    @endphp
    <div class="top-bar-wrapper">
        <div class="top-bar">
            <h2>Kelionių registravimas</h2>
            <div class="table-nav">
                @foreach($tableNames as $tableName)
                    <a href="{{ route('table.show', $tableName) }}#{{ $tableName }}">
                        {{ ucfirst(str_replace('_', ' ', $tableName)) }}
                    </a>
                @endforeach
                <!-- NEW NAV BUTTON -->
                <a href="{{ route('places') }}">Maršrutai</a>
            </div>
        </div>
    </div>
    <div class="d-flex flex-column gap-4">
        @foreach($tableNames as $i => $tableName)
            <div id="{{ $tableName }}">
                <a href="{{ route('table.show', $tableName) }}" class="card-wrapper">
                    <div class="pastel-card pastel-bg-{{ ($i % 5) + 1 }}">
                        <div class="card-title">
                            <i class="{{ $icons[$tableName] ?? 'bi bi-table' }} icon"></i>
                            {{ ucfirst(str_replace('_', ' ', $tableName)) }}
                        </div>
                        <p class="mb-2">
                            {{ $descriptions[$tableName] ?? "Peržiūrėti $tableName lentelės duomenis." }}
                        </p>
                        <span class="card-link">Atidaryti</span>
                    </div>
                </a>
            </div>
        @endforeach

        <div>
            <a href="{{ route('places') }}" class="card-wrapper">
                <div class="pastel-card pastel-bg-6">
                    <div class="card-title">
                        <i class="bi bi-map-fill icon"></i>
                        Maršrutai
                    </div>
                    <p class="mb-2">
                        Forma apie maršrutus.
                    </p>
                    <span class="card-link">Atidaryti</span>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

