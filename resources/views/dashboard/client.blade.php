@extends('layouts.app')

@section('title', 'Client Dashboard')

@section('content')
<div class="card shadow mb-4">
    <div class="card-body">
        <h1 class="h2 mb-4">Client Dashboard</h1>

        <div class="row mb-4">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="dcards-card text-center bg-primary bg-opacity-10">
                    <div class="dcards-card-body">
                        <h3 class="h5 mb-2">Active Orders</h3>
                        <p class="display-5 fw-bold">0</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4 mb-md-0">
                <div class="dcards-card text-center bg-success bg-opacity-10">
                    <div class="dcards-card-body">
                        <h3 class="h5 mb-2">Completed Orders</h3>
                        <p class="display-5 fw-bold">0</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="dcards-card text-center bg-warning bg-opacity-10">
                    <div class="dcards-card-body">
                        <h3 class="h5 mb-2">Total Spent</h3>
                        <p class="display-5 fw-bold">$0.00</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="dcards-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="h5 mb-0">Recent Orders</h3>
                <a href="#" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> New Order
                </a>
            </div>
            <div class="dcards-card-body">
                <p class="text-muted fst-italic">No orders to display</p>
            </div>
        </div>

        <div class="dcards-card">
            <div class="card-header">
                <h3 class="h5 mb-0">Order Tracking</h3>
            </div>
            <div class="dcards-card-body">
                <p class="text-muted fst-italic">No active orders to track</p>
            </div>
        </div>
    </div>
</div>
@endsection
