<?php


use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\LaundryController;
use App\Http\Controllers\UserRoleController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\PageCategoryController;
use App\Http\Controllers\Dashboard\ClientController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\RiderDashboardController;
use App\Http\Controllers\Dashboard\ClientDashboardController;
use App\Http\Controllers\Dashboard\CommonDashboardController;
use App\Http\Controllers\Dashboard\LaundryDashboardController;

// Authentication routes
//Auth::routes();

// Default route
Route::get('/', [CommonDashboardController::class, 'index'])->name('home');

// Dashboard route
Route::get('/dashboard', [CommonDashboardController::class, 'index'])->name('dashboard')->middleware('role.permission:1.1');
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard')->middleware('role.permission:1.2');
Route::get('/client/dashboard', [ClientDashboardController::class, 'index'])->name('client.dashboard')->middleware('role.permission:1.3');
Route::get('/rider/dashboard', [RiderDashboardController::class, 'index'])->name('rider.dashboard')->middleware('role.permission:1.4');
Route::get('/laundry/dashboard', [LaundryDashboardController::class, 'index'])->name('laundry.dashboard')->middleware('role.permission:1.5');

// group Authentication routes



Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register');
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
Route::delete('/profile/delete', [ProfileController::class, 'deleteProfile'])->name('profile.delete');
Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');


// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role.permission'])->group(function () {

    // Page Categories
    Route::get('/page-categories', [PageCategoryController::class, 'index'])->name('page-categories.index')->middleware('role.permission:2.1');
    Route::get('/page-categories/create', [PageCategoryController::class, 'create'])->name('page-categories.create')->middleware('role.permission:2.2');
    Route::post('/page-categories', [PageCategoryController::class, 'store'])->name('page-categories.store');
    Route::get('/page-categories/{pageCategory}/edit', [PageCategoryController::class, 'edit'])->name('page-categories.edit')->middleware('role.permission:2.3');
    Route::put('/page-categories/{pageCategory}', [PageCategoryController::class, 'update'])->name('page-categories.update');
    Route::delete('/page-categories/{pageCategory}', [PageCategoryController::class, 'destroy'])->name('page-categories.destroy');

    // Pages
    Route::get('/pages', [PageController::class, 'index'])->name('pages.index')->middleware('role.permission:2.4');
    Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create')->middleware('role.permission:2.5');
    Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
    Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit')->middleware('role.permission:2.6');
    Route::put('/pages/{page}', [PageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');

    // User Roles
    Route::get('/roles', [UserRoleController::class, 'index'])->name('roles.index')->middleware('role.permission:3.1');
    Route::get('/roles/create', [UserRoleController::class, 'create'])->name('roles.create')->middleware('role.permission:3.2');
    Route::post('/roles', [UserRoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{roleId}/edit', [UserRoleController::class, 'edit'])->name('roles.edit')->middleware('role.permission:3.3');
    Route::put('/roles/{roleId}', [UserRoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{roleId}', [UserRoleController::class, 'destroy'])->name('roles.destroy');

 // Permission management
 Route::get('/permissions/manage/{roleId}', [PermissionController::class, 'manage'])->name('permissions.manage')->middleware('role.permission:3.4');
 Route::put('/permissions/update/{roleId}', [PermissionController::class, 'update'])->name('permissions.update');
 Route::post('/permissions/search', [PermissionController::class, 'search'])->name('permissions.search');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('role.permission:4.1');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('role.permission:4.2');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{userId}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('role.permission:4.3');
    Route::put('/users/{userId}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{userId}', [UserController::class, 'delete'])->name('users.delete');

}) ;



// Client routes
Route::prefix('client')->name('client.')->middleware(['auth', 'role.permission'])->group(function () {
    Route::get('/create', [ClientController::class, 'create'])->name('create')->middleware('role.permission:3.1');
    Route::post('/store', [ClientController::class, 'store'])->name('store')->middleware('role.permission:3.1');
});

// Rider routes
Route::prefix('rider')->name('rider.')->middleware(['auth', 'role.permission'])->group(function () {
    Route::get('/assignments', [RiderController::class, 'assignments'])->name('assignments')->middleware('role.permission:4.1');
});

// Laundry routes
Route::prefix('laundry')->name('laundry.')->middleware(['auth', 'role.permission'])->group(function () {
    Route::get('/jobs', [LaundryController::class, 'jobs'])->name('jobs')->middleware('role.permission:5.1');
});


Route::get('/thanuu', function () {
    return view('test');
})->name('thanuu')->middleware('role.permission:8.3');

Route::get('/test', function () {
    return view('test');
})->name('test')->middleware('role.permission:7.1');
