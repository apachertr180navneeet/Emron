<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\VendorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [HomeController::class, 'index'])->name('/');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::name('admin.')->prefix('admin')->group(function () {
    Route::get('/', [AdminAuthController::class, 'index']);

    Route::get('login', [AdminAuthController::class, 'login'])->name('login');

    Route::post('login', [AdminAuthController::class, 'postLogin'])->name('login.post');

    Route::middleware(['admin'])->group(function () {
    	Route::get('dashboard', [AdminAuthController::class, 'adminDashboard'])->name('dashboard');

        Route::get('change-password', [AdminAuthController::class, 'changePassword'])->name('change.password');

        Route::post('update-password', [AdminAuthController::class, 'updatePassword'])->name('update.password');

        Route::get('logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('profile', [AdminAuthController::class, 'adminProfile'])->name('profile');

        Route::post('profile', [AdminAuthController::class, 'updateAdminProfile'])->name('update.profile');

        Route::get('company', [CompanyController::class, 'index'])->name('company.index');
        Route::get('company/create', [CompanyController::class, 'create'])->name('company.create');
        Route::post('company', [CompanyController::class, 'store'])->name('company.store');
        Route::get('company/{company}/edit', [CompanyController::class, 'edit'])->name('company.edit');
        Route::any('company/{company}/update', [CompanyController::class, 'update'])->name('company.update');
        Route::post('company/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('company.toggle');

    });

});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('vendor', [VendorController::class, 'index'])->name('vendor.index');
    Route::get('vendor/create', [VendorController::class, 'create'])->name('vendor.create');
    Route::post('vendor', [VendorController::class, 'store'])->name('vendor.store');
    Route::get('vendor/{vendor}/edit', [VendorController::class, 'edit'])->name('vendor.edit');
    Route::any('vendor/{vendor}/update', [VendorController::class, 'update'])->name('vendor.update');
    Route::post('vendor/{vendor}/toggle-status', [VendorController::class, 'toggleStatus'])->name('vendor.toggle');
    Route::delete('vendor/{vendor}', [VendorController::class, 'destroy'])->name('vendor.destroy');
});

Route::name('company.')->group(function () {
    Route::get('company/login', [App\Http\Controllers\Web\CompanyAuthController::class, 'login'])->name('login');
    Route::post('company/login', [App\Http\Controllers\Web\CompanyAuthController::class, 'postLogin'])->name('login.post');
});

Route::middleware(['user'])->group(function () {
    Route::get('company/dashboard', [App\Http\Controllers\Web\CompanyAuthController::class, 'dashboard'])->name('company.dashboard');
    Route::get('company/profile', [App\Http\Controllers\Web\CompanyAuthController::class, 'profile'])->name('company.profile');
    Route::post('company/profile', [App\Http\Controllers\Web\CompanyAuthController::class, 'updateProfile'])->name('company.profile.update');
    Route::get('company/change-password', [App\Http\Controllers\Web\CompanyAuthController::class, 'changePassword'])->name('company.change.password');
    Route::post('company/update-password', [App\Http\Controllers\Web\CompanyAuthController::class, 'updatePassword'])->name('company.password.update');
    Route::get('company/logout', [App\Http\Controllers\Web\CompanyAuthController::class, 'logout'])->name('company.logout');
});



