@extends('layouts/layout')
@php
//    dd($aggregatedData);
@endphp
@section('body')
    <div class="container py-5">
        <div class="d-flex justify-content-end align-items-center mb-4">
            <a href="{{ route('filters') }}" class="btn btn-outline-primary">Grįžti į ataskaitos formavimą</a>
        </div>
        @if($aggregatedData->isNotEmpty())
        @foreach($aggregatedData as $user => $data)
            @if($user !== "")
            <div class="table-responsive">
                <h3>{{$user}} - {{$aggregatedData[$user][""][0]->OrderCount}} užsakymų</h3>
                    <table class="table custom-table text-center">
                        <thead>
                            <tr>
                                <th>Užsakymo numeris</th>
                                <th>Pasirašymo data</th>
                                <th>Nutraukimo data</th>
                                <th>Vidutinis mokėjimas</th>
                                <th>Maksimalus mokėjimas</th>
                                <th>Mokėjimų suma</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $orderID => $orderData)
                                @if($orderID != "")
                                    <tr>
                                        <td>{{ $orderData[0]->OrderID }}</td>
                                        <td>{{ $orderData[0]->BeginOrderDate }}</td>
                                        <td>{{ $orderData[0]->EndOrderDate }}</td>
                                        <td>{{ $orderData[0]->AverageOrderPayment }}</td>
                                        <td>{{ $orderData[0]->MaxOrderBillSum }}</td>
                                        <td>{{ $orderData[0]->OrderPaymentSum }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end">Iš viso:</td>
                                <td>{{ $aggregatedData[$user][""][0]->OrderPaymentSum }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        @endforeach
        <div class="table-responsive">
            <table class="table custom-table text-center">
                <thead>
                    <tr>
                        <th class="text-white h-0 p-0 border-0 leading-none overflow-hidden">Užsakymo numeris</th>
                        <th class="text-white h-0 p-0 border-0 leading-none overflow-hidden">Pasirašymo data</th>
                        <th class="text-white h-0 p-0 border-0 leading-none overflow-hidden">Nutraukimo data</th>
                        <th class="text-white h-0 p-0 border-0 leading-none overflow-hidden">Vidutinis mokėjimas</th>
                        <th class="text-white h-0 p-0 border-0 leading-none overflow-hidden">Maksimalus mokėjimas</th>
                        <th class="text-white h-0 p-0 border-0 leading-none overflow-hidden">Mokėjimų suma</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="items-end">
                        <td colspan="5" class="text-end"><h4>Bendra suma:</h4></td>
                        <td>{{ $aggregatedData[""][""][0]->OrderPaymentSum  }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
            @else
            <h1>
                Tokių duomenų nėra.
            </h1>
        @endif
    </div>
@endsection
