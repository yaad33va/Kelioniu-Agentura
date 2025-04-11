<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Table: {{ $name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .custom-table {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .custom-table thead {
            background-color: #a3d5ff;
            color: #333;
        }
        .custom-table th, .custom-table td {
            padding: 14px;
            text-align: center;
            vertical-align: middle;
        }
        .custom-table tbody tr:nth-child(even) {
            background-color: #f1faff;
        }
        .custom-table tbody tr:hover {
            background-color: #e2f0ff;
        }
    </style>
</head>

<body>
<div class="container py-5">
    <div class="d-flex justify-content-end align-items-center mb-4">
        <a href="{{ route('home') }}" class="btn btn-outline-primary">Grįžti į lentelių sąrašą</a>
    </div>

    @if(count($records) > 0)
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                <tr>
                    @foreach(array_keys((array) $records[0]) as $column)
                        <th>{{ $headers[$column] ?? ucfirst(str_replace('_', ' ', $column)) }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($records as $record)
                    <tr>
                        @foreach((array) $record as $key => $value)
                            @php
                                // Default formatting
                                $formatted = $value;

                                // Format if value looks like a datetime
                                if (strtotime($value) !== false && str_contains($value, ':')) {
                                    try {
                                        $formatted = \Carbon\Carbon::parse($value)->format('Y-m-d');
                                    } catch (\Exception $e) {
                                        $formatted = $value;
                                    }
                                }

                                // Custom formatting for 'tipas'
                                if (strtolower($key) === 'tipas') {
                                    $formatted = str_replace('_', ' ', $formatted);
                                    $formatted = ucfirst($formatted);
                                }
                            @endphp
                            <td>{{ $formatted }}</td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info text-center">
            Šioje lentelėje įrašų nerasta.
        </div>
    @endif
</div>
</body>
</html>
