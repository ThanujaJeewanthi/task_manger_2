@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="card shadow mb-4">
    <div class="card-body">
        <h1 class="h2 mb-4">Welcome to Spin App</h1>

        <div class="row mb-4">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card bg-primary bg-opacity-10">
                    <div class="card-body">
                        <h3 class="h5 mb-2">User Information</h3>
                        <p><strong>Username:</strong> {{ $user->username }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Phone:</strong> {{ $user->phone_number }}</p>
                        <p class="mb-0"><strong>Role:</strong> {{ $role->name }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card bg-success bg-opacity-10">
                    <div class="card-body">
                        <h3 class="h5 mb-2">Quick Actions</h3>
                        <ul class="list-unstyled mb-0">
                            @if($user->hasRole('client'))
                                <li class="mb-2"><a href="#" class="text-primary">Create New Order</a></li>
                                <li class="mb-2"><a href="#" class="text-primary">View My Orders</a></li>
                            @elseif($user->hasRole('rider'))
                                <li class="mb-2"><a href="#" class="text-primary">View Assigned Pickups</a></li>
                                <li class="mb-2"><a href="#" class="text-primary">View Pending Deliveries</a></li>
                            @elseif($user->hasRole('laundry'))
                                <li class="mb-2"><a href="#" class="text-primary">View New Jobs</a></li>
                                <li class="mb-2"><a href="#" class="text-primary">Process Completed Jobs</a></li>
                            {{-- @elseif($user->hasRole('admin'))
                                <li class="mb-2"><a href="{{ route('users.index') }}" class="text-primary">Manage Users</a></li>
                                <li class="mb-2"><a href="{{ route('roles.index') }}" class="text-primary">Manage Roles</a></li> --}}
                            @endif
                            <li><a href="#" class="text-primary">Edit Profile</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-info bg-opacity-10">
                    <div class="card-body">
                        <h3 class="h5 mb-2">System Notifications</h3>
                        <p class="text-muted fst-italic mb-0">No new notifications</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-light">
            <div class="card-body">
                <h3 class="h5 mb-2">About Spin App</h3>
                <p class="mb-0">Spin App is a comprehensive laundry management system that connects clients, riders, and laundry services through a seamless digital platform. Our goal is to simplify the laundry process from pickup to delivery.</p>
            </div>
        </div>
    </div>
</div>
@endsection
