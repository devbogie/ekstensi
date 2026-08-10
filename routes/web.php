<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ExtensionController;
use App\Http\Controllers\Admin\FloorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/direktori/data', [PublicController::class, 'data'])->name('directory.data');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

    Route::middleware(['auth', 'throttle:120,1'])->group(function () {
        Route::get('/', [ExtensionController::class, 'index'])->name('dashboard');

        Route::get('profile', [AuthController::class, 'showProfile'])->name('profile');
        Route::put('profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');

        Route::get('extensions/data', [ExtensionController::class, 'data'])->name('extensions.data');
        Route::post('extensions', [ExtensionController::class, 'store'])->name('extensions.store');
        Route::put('extensions/{extension}', [ExtensionController::class, 'update'])->name('extensions.update');
        Route::delete('extensions/{extension}', [ExtensionController::class, 'destroy'])->name('extensions.destroy');
        Route::get('extensions/export', [ExtensionController::class, 'export'])->name('extensions.export');
        Route::get('extensions/import-template', [ExtensionController::class, 'downloadTemplate'])->name('extensions.import-template');
        Route::post('extensions/import', [ExtensionController::class, 'import'])->name('extensions.import');

        Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('departments/data', [DepartmentController::class, 'data'])->name('departments.data');
        Route::post('departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::put('departments/{id}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('departments/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

        Route::get('floors', [FloorController::class, 'index'])->name('floors.index');
        Route::get('floors/data', [FloorController::class, 'data'])->name('floors.data');
        Route::post('floors', [FloorController::class, 'store'])->name('floors.store');
        Route::put('floors/{id}', [FloorController::class, 'update'])->name('floors.update');
        Route::delete('floors/{id}', [FloorController::class, 'destroy'])->name('floors.destroy');

        Route::get('branches', [BranchController::class, 'index'])->name('branches.index');
        Route::get('branches/data', [BranchController::class, 'data'])->name('branches.data');
        Route::post('branches', [BranchController::class, 'store'])->name('branches.store');
        Route::put('branches/{id}', [BranchController::class, 'update'])->name('branches.update');
        Route::delete('branches/{id}', [BranchController::class, 'destroy'])->name('branches.destroy');
});
});
