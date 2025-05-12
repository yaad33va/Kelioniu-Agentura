@extends('layouts/layout')
@section('body')
    <div class="container py-5">
        <div class="d-flex justify-content-end align-items-center mb-4">
            <a href="{{ route('home') }}" class="btn btn-outline-primary">Grįžti į lentelių sąrašą</a>
        </div>
        <form action="{{route('aggregates')}}" method="POST">
            @csrf
            @method('POST')
            <h4>Pasirašymo data</h4>
            <div class="form-group">
                <label for="beginDate">Nuo</label>
                <input type="date" class="form-control" id="beginDate" name="beginDate">
            </div>
            <div class="form-group">
                <label for="endDate">Iki</label>
                <input type="date" class="form-control" id="endDate" name="endDate">
            </div>
            <h4 class="mt-4">Nutraukimo data</h4>
            <div class="form-group">
                <label for="beginEndDate">Nuo</label>
                <input type="date" class="form-control" id="beginEndDate" name="beginEndDate">
            </div>
            <div class="form-group">
                <label for="endEndDate">Iki</label>
                <input type="date" class="form-control" id="endEndDate" name="endEndDate">
            </div>
            <h4 class="mt-4">Sąskaitos suma</h4>
            <div class="form-group">
                <label for="maxSum">Didžiausia sąskaitos suma</label>
                <input type="number" class="form-control" id="maxSum" name="maxSum">
            </div>
            <button type="submit" class="btn btn-primary mt-4">Formuoti ataskaitą</button>
        </form>
    </div>
@endsection
