@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-semibold mb-4">Welcome to Spin App</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-blue-100 p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">User Information</h3>
            <p><strong>Username:</strong> {{ $user->username }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Phone:</strong> {{ $user->phone_number }}</p>
            <p><strong>Role:</strong> {{ $role->name }}</p>
        </div>

        <div class="bg-green-100 p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Quick Actions</h3>
            <ul class="space-y-2">
                @if($user->hasRole('client'))
                    <li><a href="#" class="text-blue-600 hover:underline">Create New Order</a></li>
                    <li><a href="#" class="text-blue-600 hover:underline">View My Orders</a></li>
                @elseif($user->hasRole('rider'))
                    <li><a href="#" class="text-blue-600 hover:underline">View Assigned Pickups</a></li>
                    <li><a href="#" class="text-blue-600 hover:underline">View Pending Deliveries</a></li>
                @elseif($user->hasRole('laundry'))
                    <li><a href="#" class="text-blue-600 hover:underline">View New Jobs</a></li>
                    <li><a href="#" class="text-blue-600 hover:underline">Process Completed Jobs</a></li>
                {{-- @elseif($user->hasRole('admin'))
                    <li><a href="{{ route('users.index') }}" class="text-blue-600 hover:underline">Manage Users</a></li>
                    <li><a href="{{ route('roles.index') }}" class="text-blue-600 hover:underline">Manage Roles</a></li> --}}
                @endif
                <li><a href="#" class="text-blue-600 hover:underline">Edit Profile</a></li>
            </ul>
        </div>

        <div class="bg-purple-100 p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">System Notifications</h3>
            <p class="text-gray-500 italic">No new notifications</p>
        </div>
    </div>

    <div class="bg-gray-100 p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-2">About Spin App</h3>
        <p>Spin App is a comprehensive laundry management system that connects clients, riders, and laundry services through a seamless digital platform. Our goal is to simplify the laundry process from pickup to delivery.</p>
    </div>
</div>
@endsection
