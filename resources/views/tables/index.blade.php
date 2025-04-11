<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .pastel-card {
            border: none;
            border-radius: 16px;
            padding: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            font-size: 1.1rem;
        }

        .pastel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .pastel-bg-1 { background-color: #f8d7da; }
        .pastel-bg-2 { background-color: #d1ecf1; }
        .pastel-bg-3 { background-color: #d4edda; }
        .pastel-bg-4 { background-color: #fff3cd; }
        .pastel-bg-5 { background-color: #e2e3e5; }

        a.card-wrapper {
            text-decoration: none;
            color: inherit;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .card-link {
            font-weight: 500;
            color: #333;
            text-decoration: underline;
        }

        .top-bar-wrapper {
            width: 100%;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px 40px;
            margin-bottom: 2rem;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .top-bar h2 {
            font-weight: 700;
            margin: 0;
        }

        .table-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .table-nav a {
            padding: 6px 12px;
            border-radius: 6px;
            background-color: #f1f1f1;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }

        .table-nav a:hover {
            background-color: #ddd;
        }

        html {
            scroll-behavior: smooth;
        }

        .icon {
            font-size: 1.5rem;
            margin-right: 10px;
        }
    </style>
</head>

<body>

<!-- Top header bar -->
<div class="top-bar-wrapper">
    <div class="top-bar">
        <h2>Kelionių registravimas</h2>
        <div class="table-nav">
            @foreach($tableNames as $tableName)
                <a href="{{ route('table.show', $tableName) }}#{{ $tableName }}">
                    {{ ucfirst(str_replace('_', ' ', $tableName)) }}
                </a>
            @endforeach
        </div>
    </div>
</div>

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
    </div>
</div>

</body>
</html>
