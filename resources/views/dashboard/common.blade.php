@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid ">
    <div class="card shadow mb-4 ">
        <div class="card-body">
            <h2 class="h2 mb-4">Welcome to Task Manager</h2>

            <div class="row " style="overflow-x:unset;">
                <div class="col-md-6 col-lg-4 col-sm-12 mb-4">
                    <div class="dcards-card bg-primary bg-opacity-10">
                        <div class="dcards-card-body">
                            <h3 class="h5 mb-2">User Information</h3>
                            <p><strong>Username:</strong> {{ $user->username }}</p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                            <p><strong>Phone:</strong> {{ $user->phone_number }}</p>
                            <p class="mb-0"><strong>Role:</strong> {{ $role->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 col-sm-12 mb-4">
                    <div class="dcards-card bg-success bg-opacity-10">
                        <div class="dcards-card-body">
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
                                @endif
                                <li><a href="#" class="text-primary">Edit Profile</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 col-sm-12 mb-4">
                    <div class="dcards-card bg-info bg-opacity-10">
                        <div class="dcards-card-body">
                            <h3 class="h5 mb-2">System Notifications</h3>
                            <p class="text-muted fst-italic mb-0">No new notifications</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="dcards-card bg-primary-subtle bg-opacity-20">
                        <div class="dcards-card-body">
                            <h3 class="h5 mb-2">About Task Manager</h3>
                            <p class="mb-0">Task Manager is a comprehensive solution for managing tasks and projects efficiently. Our platform connects users with the tools they need to streamline their workflow and enhance productivity.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
