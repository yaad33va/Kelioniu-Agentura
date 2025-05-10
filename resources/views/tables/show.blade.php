@extends('layouts/layout')
@section('body')
    <div class="container py-5">
        <div class="d-flex justify-content-end align-items-center mb-4">
            <a href="{{ route('home') }}" class="btn btn-outline-primary">Grįžti į lentelių sąrašą</a>
        </div>

        @if(count($records) > 0)
            <div class="table-responsive">
                <table class="table custom-table text-center">
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
                                    $formatted = $value;

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
@endsection
