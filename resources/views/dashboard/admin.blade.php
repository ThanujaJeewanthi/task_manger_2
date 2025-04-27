@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-semibold mb-4">Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-blue-100 p-4 rounded-lg shadow text-center">
            <h3 class="text-lg font-semibold mb-2">Total Users</h3>
            <p class="text-3xl font-bold">{{ $totalUsers }}</p>
        </div>

        <div class="bg-green-100 p-4 rounded-lg shadow text-center">
            <h3 class="text-lg font-semibold mb-2">Active Users</h3>
            <p class="text-3xl font-bold">{{ $activeUsers }}</p>
        </div>

        <div class="bg-yellow-100 p-4 rounded-lg shadow text-center">
            <h3 class="text-lg font-semibold mb-2">Total Orders</h3>
            <p class="text-3xl font-bold">0</p>
        </div>

        <div class="bg-purple-100 p-4 rounded-lg shadow text-center">
            <h3 class="text-lg font-semibold mb-2">Completed Orders</h3>
            <p class="text-3xl font-bold">0</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white border rounded-lg shadow">
            <div class="border-b p-4">
                <h3 class="text-lg font-semibold">System Management</h3>
            </div>
            <div class="p-4">
                <ul class="space-y-2">
                    {{-- <li><a href="{{ route('users.index') }}" class="text-blue-600 hover:underline flex items-center"><i class="fas fa-users mr-2"></i> User Management</a></li>
                    <li><a href="{{ route('roles.index') }}" class="text-blue-600 hover:underline flex items-center"><i class="fas fa-user-tag mr-2"></i> Role Management</a></li>
                    <li><a href="{{ route('privileges.index') }}" class="text-blue-600 hover:underline flex items-center"><i class="fas fa-key mr-2"></i> Special Privileges</a></li>
                    <li><a href="{{ route('pages.index') }}" class="text-blue-600 hover:underline flex items-center"><i class="fas fa-file mr-2"></i> Page Management</a></li>
                    <li><a href="{{ route('page-categories.index') }}" class="text-blue-600 hover:underline flex items-center"><i class="fas fa-folder mr-2"></i> Page Categories</a></li> --}}
                </ul>
            </div>
        </div>

        <div class="bg-white border rounded-lg shadow">
            <div class="border-b p-4">
                <h3 class="text-lg font-semibold">Recent Activity</h3>
            </div>
            <div class="p-4">
                <p class="text-gray-500 italic">No recent activity to display</p>
            </div>
        </div>
    </div>
</div>
@endsection
