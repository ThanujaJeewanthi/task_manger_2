@extends('layouts.app')

@section('title', 'Laundry Dashboard')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-semibold mb-4">Laundry Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-blue-100 p-4 rounded-lg shadow text-center">
            <h3 class="text-lg font-semibold mb-2">New Jobs</h3>
            <p class="text-3xl font-bold">0</p>
        </div>

        <div class="bg-yellow-100 p-4 rounded-lg shadow text-center">
            <h3 class="text-lg font-semibold mb-2">In Progress</h3>
            <p class="text-3xl font-bold">0</p>
        </div>

        <div class="bg-green-100 p-4 rounded-lg shadow text-center">
            <h3 class="text-lg font-semibold mb-2">Ready for Pickup</h3>
            <p class="text-3xl font-bold">0</p>
        </div>

        <div class="bg-purple-100 p-4 rounded-lg shadow text-center">
            <h3 class="text-lg font-semibold mb-2">Completed Today</h3>
            <p class="text-3xl font-bold">0</p>
        </div>
    </div>

    <div class="bg-white border rounded-lg shadow mb-6">
        <div class="border-b p-4">
            <h3 class="text-lg font-semibold">New Laundry Jobs</h3>
        </div>
        <div class="p-4">
            <p class="text-gray-500 italic">No new jobs available</p>
        </div>
    </div>

    <div class="bg-white border rounded-lg shadow">
        <div class="border-b p-4">
            <h3 class="text-lg font-semibold">Jobs In Progress</h3>
        </div>
        <div class="p-4">
            <p class="text-gray-500 italic">No jobs in progress</p>
        </div>
    </div>
</div>
@endsection



