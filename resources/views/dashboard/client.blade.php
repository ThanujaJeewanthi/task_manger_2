@extends('layouts.app')

@section('title', 'Client Dashboard')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-semibold mb-4">Client Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-blue-100 p-4 rounded-lg shadow text-center">
            <h3 class="text-lg font-semibold mb-2">Active Orders</h3>
            <p class="text-3xl font-bold">0</p>
        </div>

        <div class="bg-green-100 p-4 rounded-lg shadow text-center">
            <h3 class="text-lg font-semibold mb-2">Completed Orders</h3>
            <p class="text-3xl font-bold">0</p>
        </div>

        <div class="bg-yellow-100 p-4 rounded-lg shadow text-center">
            <h3 class="text-lg font-semibold mb-2">Total Spent</h3>
            <p class="text-3xl font-bold">$0.00</p>
        </div>
    </div>

    <div class="bg-white border rounded-lg shadow mb-6">
        <div class="border-b p-4 flex justify-between items-center">
            <h3 class="text-lg font-semibold">Recent Orders</h3>
            <a href="#" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i> New Order
            </a>
        </div>
        <div class="p-4">
            <p class="text-gray-500 italic">No orders to display</p>
        </div>
    </div>

    <div class="bg-white border rounded-lg shadow">
        <div class="border-b p-4">
            <h3 class="text-lg font-semibold">Order Tracking</h3>
        </div>
        <div class="p-4">
            <p class="text-gray-500 italic">No active orders to track</p>
        </div>
    </div>
</div>
@endsection

