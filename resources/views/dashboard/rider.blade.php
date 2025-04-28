@extends('layouts.app')

@section('title', 'Rider Dashboard')

@section('content')
<div class="card shadow mb-4">
    <div class="card-body">
        <h1 class="h2 mb-4">Rider Dashboard</h1>

        <div class="row mb-4">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card text-center bg-primary bg-opacity-10">
                    <div class="card-body">
                        <h3 class="h5 mb-2">Pending Pickups</h3>
                        <p class="display-5 fw-bold">0</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card text-center bg-success bg-opacity-10">
                    <div class="card-body">
                        <h3 class="h5 mb-2">Pending Deliveries</h3>
                        <p class="display-5 fw-bold">0</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center bg-warning bg-opacity-10">
                    <div class="card-body">
                        <h3 class="h5 mb-2">Completed Today</h3>
                        <p class="display-5 fw-bold">0</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3 class="h5 mb-0">Assigned Pickups</h3>
            </div>
            <div class="card-body">
                <p class="text-muted fst-italic">No pickups assigned</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="h5 mb-0">Assigned Deliveries</h3>
            </div>
            <div class="card-body">
                <p class="text-muted fst-italic">No deliveries assigned</p>
            </div>
        </div>
    </div>
</div>
@endsection
