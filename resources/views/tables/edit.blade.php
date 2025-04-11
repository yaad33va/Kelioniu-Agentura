@extends('layouts.app')

@section('content')
    <h2>F1 tipo forma – Užsakymai</h2>

    <table class="table">
        <thead>
        <tr>
            <th>Užsakymo ID</th>
            <th>Suma</th>
            <th>Kelionė</th>
            <th>Veiksmai</th>
        </tr>
        </thead>
        <tbody>
        @foreach($maršrutai as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->total }}</td>
                <td>{{ $order->trip->title ?? 'Nenurodyta' }}</td>
                <td>
                    <a href="{{ route('orders.edit', $order->id) }}">Redaguoti</a> |
                    <form action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Ištrinti?')">Šalinti</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <a href="{{ route('orders.create') }}">➕ Pridėti naują užsakymą</a>
@endsection
