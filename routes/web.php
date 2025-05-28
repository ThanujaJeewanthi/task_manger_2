<?php


use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;



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
use App\Http\Controllers\Job\JobController;
use App\Http\Controllers\Job\JobTypeController;
use App\Http\Controllers\Job\JobOptionController;

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
Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');


// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // Page Categories
    Route::get('/page-categories', [PageCategoryController::class, 'index'])
        ->name('page-categories.index')
        ->middleware('role.permission:2.1');
    Route::get('/page-categories/create', [PageCategoryController::class, 'create'])
        ->name('page-categories.create')
        ->middleware('role.permission:2.2');
    Route::post('/page-categories', [PageCategoryController::class, 'store'])
        ->name('page-categories.store')
        ->middleware('role.permission:2.2');
    Route::get('/page-categories/{pageCategory}/edit', [PageCategoryController::class, 'edit'])
        ->name('page-categories.edit')
        ->middleware('role.permission:2.3');
    Route::put('/page-categories/{pageCategory}', [PageCategoryController::class, 'update'])
        ->name('page-categories.update')
        ->middleware('role.permission:2.3');
    Route::delete('/page-categories/{pageCategory}', [PageCategoryController::class, 'destroy'])
        ->name('page-categories.destroy')
        ->middleware('role.permission:2.1');
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
});

Route::middleware(['auth'])->group(function () {

Route::get('/logs', [App\Http\Controllers\LogController::class, 'index'])->name('logs.index')->middleware('role.permission:5.1');
    Route::get('/logs/{id}', [App\Http\Controllers\LogController::class, 'show'])->name('logs.show');
    Route::post('/logs/clear', [App\Http\Controllers\LogController::class, 'clear'])->name('logs.clear');
});

//companies routes
Route::prefix('companies')->name('companies.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\CompanyController::class, 'index'])->name('index')->middleware('role.permission:5.1');
    Route::get('/create', [\App\Http\Controllers\CompanyController::class, 'create'])->name('create')->middleware('role.permission:5.2');
    Route::get('/{company}', [\App\Http\Controllers\CompanyController::class, 'show'])->name('show')->middleware('role.permission:5.3');
    Route::post('/', [\App\Http\Controllers\CompanyController::class, 'store'])->name('store')->middleware('role.permission:5.2');
    Route::get('/{company}/edit', [\App\Http\Controllers\CompanyController::class, 'edit'])->name('edit')->middleware('role.permission:5.4');
    Route::put('/{company}', [\App\Http\Controllers\CompanyController::class, 'update'])->name('update')->middleware('role.permission:5.4');
    Route::delete('/{company}', [\App\Http\Controllers\CompanyController::class, 'destroy'])->name('destroy')->middleware('role.permission:5.4');
});

//clients
Route::prefix('clients')->name('clients.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\ClientController::class, 'index'])->name('index')->middleware('role.permission:7.1');
    Route::get('/create', [\App\Http\Controllers\ClientController::class, 'create'])->name('create')->middleware('role.permission:7.2');
    Route::post('/', [\App\Http\Controllers\ClientController::class, 'store'])->name('store')->middleware('role.permission:7.2');
    Route::get('/{client}/edit', [\App\Http\Controllers\ClientController::class, 'edit'])->name('edit')->middleware('role.permission:7.3');
    Route::put('/{client}', [\App\Http\Controllers\ClientController::class, 'update'])->name('update')->middleware('role.permission:7.3');
    Route::delete('/{client}', [\App\Http\Controllers\ClientController::class, 'destroy'])->name('destroy')->middleware('role.permission:7.3');
});
//employees
Route::prefix('employees')->name('employees.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\EmployeeController::class, 'index'])->name('index')->middleware('role.permission:6.1');
    Route::get('/create', [\App\Http\Controllers\EmployeeController::class, 'create'])->name('create')->middleware('role.permission:6.2');
    Route::get('/{employee}', [\App\Http\Controllers\EmployeeController::class, 'show'])->name('show')->middleware('role.permission:6.3');
    Route::post('/', [\App\Http\Controllers\EmployeeController::class, 'store'])->name('store')->middleware('role.permission:6.2');
    Route::get('/{employee}/edit', [\App\Http\Controllers\EmployeeController::class, 'edit'])->name('edit')->middleware('role.permission:6.4');
    Route::put('/{employee}', [\App\Http\Controllers\EmployeeController::class, 'update'])->name('update')->middleware('role.permission:6.4');
    Route::delete('/{employee}', [\App\Http\Controllers\EmployeeController::class, 'destroy'])->name('destroy')->middleware('role.permission:6.4');
});
//jobs
Route::prefix('jobs')->name('jobs.')->middleware(['auth'])->group(function () {
    Route::get('/', [JobController::class, 'index'])->name('index')->middleware('role.permission:11.7');
    Route::get('/create', [JobController::class, 'create'])->name('create')->middleware('role.permission:11.8');
    Route::post('/', [JobController::class, 'store'])->name('store')->middleware('role.permission:11.8');
    Route::get('/{job}/edit', [JobController::class, 'edit'])->name('edit')->middleware('role.permission:11.9');
    Route::put('/{job}', [JobController::class, 'update'])->name('update')->middleware('role.permission:11.9');
    Route::delete('/{job}', [JobController::class, 'destroy'])->name('destroy')->middleware('role.permission:11.9');
    Route::get('/{job}', [JobController::class, 'show'])->name('show')->middleware('role.permission:11.10');
});
//job options
Route::prefix('job-options')->name('job-options.')->middleware(['auth'])->group(function () {
    Route::get('/', [JobOptionController::class, 'index'])->name('index')->middleware('role.permission:11.4');
    Route::get('/create', [JobOptionController::class, 'create'])->name('create')->middleware('role.permission:11.5');
    Route::post('/', [JobOptionController::class, 'store'])->name('store')->middleware('role.permission:11.5');
    Route::get('/{jobOption}/edit', [JobOptionController::class, 'edit'])->name('edit')->middleware('role.permission:11.6');
    Route::put('/{jobOption}', [JobOptionController::class, 'update'])->name('update')->middleware('role.permission:11.6');
    Route::delete('/{jobOption}', [JobOptionController::class, 'destroy'])->name('destroy')->middleware('role.permission:11.6');
});

Route::prefix('job-types')->name('job-types.')->middleware(['auth'])->group(function () {
    Route::get('/', [JobTypeController::class, 'index'])->name('index')->middleware('role.permission:11.1');
    Route::get('/create', [JobTypeController::class, 'create'])->name('create')->middleware('role.permission:11.2');
    Route::post('/', [JobTypeController::class, 'store'])->name('store')->middleware('role.permission:11.2');
    Route::get('/{jobType}/edit', [JobTypeController::class, 'edit'])->name('edit')->middleware('role.permission:11.3');
    Route::put('/{jobType}', [JobTypeController::class, 'update'])->name('update')->middleware('role.permission:11.3');
    Route::delete('/{jobType}', [JobTypeController::class, 'destroy'])->name('destroy')->middleware('role.permission:11.3');
});

//items
Route::prefix('items')->name('items.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\ItemController::class, 'index'])->name('index')->middleware('role.permission:10.1');
    Route::get('/create', [\App\Http\Controllers\ItemController::class, 'create'])->name('create')->middleware('role.permission:10.2');
    Route::post('/', [\App\Http\Controllers\ItemController::class, 'store'])->name('store')->middleware('role.permission:10.2');
    Route::get('/{item}/edit', [\App\Http\Controllers\ItemController::class, 'edit'])->name('edit')->middleware('role.permission:10.3');
    Route::put('/{item}', [\App\Http\Controllers\ItemController::class, 'update'])->name('update')->middleware('role.permission:10.3');
    Route::delete('/{item}', [\App\Http\Controllers\ItemController::class, 'destroy'])->name('destroy')->middleware('role.permission:10.3');
});
//equipments
Route::prefix('equipments')->name('equipments.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\EquipmentController::class, 'index'])->name('index')->middleware('role.permission:9.1');
    Route::get('/create', [\App\Http\Controllers\EquipmentController::class, 'create'])->name('create')->middleware('role.permission:9.2');
    Route::post('/', [\App\Http\Controllers\EquipmentController::class, 'store'])->name('store')->middleware('role.permission:9.2');
    Route::get('/{equipment}/edit', [\App\Http\Controllers\EquipmentController::class, 'edit'])->name('edit')->middleware('role.permission:9.3');
    Route::put('/{equipment}', [\App\Http\Controllers\EquipmentController::class, 'update'])->name('update')->middleware('role.permission:9.3');
    Route::delete('/{equipment}', [\App\Http\Controllers\EquipmentController::class, 'destroy'])->name('destroy')->middleware('role.permission:9.3');
});
//suppliers
Route::prefix('suppliers')->name('suppliers.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\SupplierController::class, 'index'])->name('index')->middleware('role.permission:8.1');
    Route::get('/create', [\App\Http\Controllers\SupplierController::class, 'create'])->name('create')->middleware('role.permission:8.2');
    Route::post('/', [\App\Http\Controllers\SupplierController::class, 'store'])->name('store')->middleware('role.permission:8.2');
    Route::get('/{supplier}/edit', [\App\Http\Controllers\SupplierController::class, 'edit'])->name('edit')->middleware('role.permission:8.3');
    Route::put('/{supplier}', [\App\Http\Controllers\SupplierController::class, 'update'])->name('update')->middleware('role.permission:8.3');
    Route::delete('/{supplier}', [\App\Http\Controllers\SupplierController::class, 'destroy'])->name('destroy')->middleware('role.permission:8.3');
});

//tasks
Route::prefix('jobs')->name('jobs.tasks.')->middleware(['auth'])->group(function () {
    Route::get('/{job}/tasks/', [JobController::class, 'index'])->name('index')->middleware('role.permission:11.11');
    Route::get('/{job}/tasks/create', [JobController::class, 'createTask'])->name('create')->middleware('role.permission:11.12');
    Route::post('/{job}/tasks', [JobController::class, 'storeTask'])->name('store')->middleware('role.permission:11.12');
    Route::get('/{job}/tasks/{task}/edit', [JobController::class, 'editTask'])->name('edit')->middleware('role.permission:11.13');
    Route::put('/{job}/tasks/{task}', [JobController::class, 'updateTask'])->name('update')->middleware('role.permission:11.13');
    Route::delete('/{job}/tasks/{task}', [JobController::class, 'destroyTask'])->name('destroy')->middleware('role.permission:11.13');

});

//items for jobs
Route::prefix('jobs')->name('jobs.items.')->middleware(['auth'])->group(function () {
    Route::get('/{job}/items/create', [JobController::class, 'createJobItem'])->name('create')->middleware('role.permission:11.14');
    Route::post('/{job}/items', [JobController::class, 'storeJobItem'])->name('store')->middleware('role.permission:11.14');
});


Route::get('/jobs/job-types/{jobTypeId}/options', [JobController::class, 'getJobTypeOptions'])
    ->name('jobs.job-type-options');



// Route::fallback(function () {
//     return 'Fallback';
// });


// Client routes
// Route::prefix('client')->name('client.')->middleware(['auth', 'role.permission'])->group(function () {
//     Route::get('/create', [ClientController::class, 'create'])->name('create')->middleware('role.permission:3.1');
//     Route::post('/store', [ClientController::class, 'store'])->name('store')->middleware('role.permission:3.1');
// });

// Rider routes
// Route::prefix('rider')->name('rider.')->middleware(['auth', 'role.permission'])->group(function () {
//     Route::get('/assignments', [RiderController::class, 'assignments'])->name('assignments')->middleware('role.permission:4.1');
// });

// Laundry routes
// Route::prefix('laundry')->name('laundry.')->middleware(['auth', 'role.permission'])->group(function () {
//     Route::get('/jobs', [LaundryController::class, 'jobs'])->name('jobs')->middleware('role.permission:5.1');
// });


// Route::get('/thanuu', function () {
//     return view('test');
// })->name('thanuu')->middleware('role.permission:8.3');

// Route::get('/test', function () {
//     return view('test');
// })->name('test')->middleware('role.permission:7.1');
