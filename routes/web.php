<?php


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PageController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\LaundryController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PageCategoryController;
use App\Http\Controllers\Dashboard\ClientController;
use App\Http\Controllers\Dashboard\CommonDashboardController;




// Authentication routes
Auth::routes();

// Dashboard routes
Route::get('/dashboard', [CommonDashboardController::class, 'index'])->name('dashboard');

// Admin routes with permission middleware
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role.permission'])->group(function () {
    // Permission management
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('role.permission:2.1');
    Route::get('/permissions/{role}', [PermissionController::class, 'manage'])->name('permissions.manage')->middleware('role.permission:2.2');
    Route::put('/permissions/{role}', [PermissionController::class, 'update'])->name('permissions.update')->middleware('role.permission:2.3');

    // Other admin routes...
});

// Client routes with permission middleware
Route::prefix('client')->name('client.')->middleware(['auth', 'role.permission'])->group(function () {
    Route::get('/create', [ClientController::class, 'create'])->name('create')->middleware('role.permission:3.1');
    Route::post('/store', [ClientController::class, 'store'])->name('store')->middleware('role.permission:3.1');
    // Other client routes...
});

// Rider routes with permission middleware
Route::prefix('rider')->name('rider.')->middleware(['auth', 'role.permission'])->group(function () {
    Route::get('/assignments', [RiderController::class, 'assignments'])->name('assignments')->middleware('role.permission:4.1');
    // Other rider routes...
});

// Laundry routes with permission middleware
Route::prefix('laundry')->name('laundry.')->middleware(['auth', 'role.permission'])->group(function () {
    Route::get('/jobs', [LaundryController::class, 'jobs'])->name('jobs')->middleware('role.permission:5.1');
    // Other laundry routes...
});


Route::prefix('admin')->name('admin.')->middleware(['auth', 'role.permission'])->group(function () {
    // Page Categories
    Route::get('/page-categories', [PageCategoryController::class, 'index'])->name('page-categories.index')->middleware('role.permission:2.4');
    Route::get('/page-categories/create', [PageCategoryController::class, 'create'])->name('page-categories.create')->middleware('role.permission:2.5');
    Route::post('/page-categories', [PageCategoryController::class, 'store'])->name('page-categories.store')->middleware('role.permission:2.5');
    Route::get('/page-categories/{id}/edit', [PageCategoryController::class, 'edit'])->name('page-categories.edit')->middleware('role.permission:2.6');
    Route::put('/page-categories/{id}', [PageCategoryController::class, 'update'])->name('page-categories.update')->middleware('role.permission:2.6');
    Route::delete('/page-categories/{id}', [PageCategoryController::class, 'destroy'])->name('page-categories.destroy')->middleware('role.permission:2.7');

    // Pages
    Route::get('/pages', [PageController::class, 'index'])->name('pages.index')->middleware('role.permission:2.8');
    Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create')->middleware('role.permission:2.9');
    Route::post('/pages', [PageController::class, 'store'])->name('pages.store')->middleware('role.permission:2.9');
    Route::get('/pages/{id}/edit', [PageController::class, 'edit'])->name('pages.edit')->middleware('role.permission:2.10');
    Route::put('/pages/{id}', [PageController::class, 'update'])->name('pages.update')->middleware('role.permission:2.10');
    Route::delete('/pages/{id}', [PageController::class, 'destroy'])->name('pages.destroy')->middleware('role.permission:2.11');
});


Route::prefix('admin')->name('admin.')->middleware(['auth', 'role.permission'])->group(function () {
    // User Roles
    Route::get('/roles', [UserRoleController::class, 'index'])->name('roles.index')->middleware('role.permission:2.2');
    Route::get('/roles/create', [UserRoleController::class, 'create'])->name('roles.create')->middleware('role.permission:2.2');
    Route::post('/roles', [UserRoleController::class, 'store'])->name('roles.store')->middleware('role.permission:2.2');
    Route::get('/roles/{id}/edit', [UserRoleController::class, 'edit'])->name('roles.edit')->middleware('role.permission:2.2');
    Route::put('/roles/{id}', [UserRoleController::class, 'update'])->name('roles.update')->middleware('role.permission:2.2');
    Route::delete('/roles/{id}', [UserRoleController::class, 'destroy'])->name('roles.destroy')->middleware('role.permission:2.2');
    Route::post('/roles/clone-permissions', [UserRoleController::class, 'clonePermissions'])->name('roles.clone-permissions')->middleware('role.permission:2.2');
});
