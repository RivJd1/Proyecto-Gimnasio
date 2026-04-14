<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupportProviderController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TrainingPlanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    // Recepcionista y admin
    Route::middleware(['role:admin,receptionist'])->group(function () {
        Route::resource('clients', ClientController::class);
        Route::resource('memberships', MembershipController::class);
        Route::resource('payments', PaymentController::class);
        Route::resource('membership-plans', MembershipPlanController::class);
        Route::resource('support-providers', SupportProviderController::class);
    });

    // Admin y entrenadores
    Route::middleware(['role:admin,trainer'])->group(function () {
        Route::resource('training-plans', TrainingPlanController::class);
        Route::resource('assignments', AssignmentController::class);
    });

    // Solo admin
    Route::middleware(['role:admin'])->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('users', UserController::class);
            Route::resource('trainers', TrainerController::class);
            Route::get('finances', [FinanceController::class, 'index'])->name('finances.index');
        });
    });

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});

require __DIR__.'/auth.php';
