<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Controllers\ClientEquipmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentQrCodeController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\InspectionTemplateController;
use App\Http\Controllers\ProjectTrackerController;
use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\WorkplaceController;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\EnsureWorkplaceUser;
use Illuminate\Support\Facades\Route;

Route::get('/', PublicSiteController::class)
    ->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');

    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::middleware(['auth', EnsureSuperAdmin::class])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');
    Route::get('/project-tracker', ProjectTrackerController::class)
        ->name('project-tracker.index');
    Route::get('/scan', ScanController::class)
        ->name('scanner.index');

    Route::resource('workplaces', WorkplaceController::class);
    Route::resource('workplaces.inspection-templates', InspectionTemplateController::class)
        ->shallow()
        ->except(['destroy']);
    Route::delete('inspection-templates/{inspection_template}', [InspectionTemplateController::class, 'destroy'])
        ->name('inspection-templates.destroy');
    Route::resource('workplaces.equipment', EquipmentController::class)
        ->shallow()
        ->except(['index']);
    Route::get('equipment/{equipment}/qr-code', [EquipmentQrCodeController::class, 'show'])
        ->name('equipment.qr-code.show');
    Route::get('equipment/{equipment}/qr-code/download', [EquipmentQrCodeController::class, 'download'])
        ->name('equipment.qr-code.download');
    Route::post('equipment/{equipment}/qr-code/regenerate', [EquipmentQrCodeController::class, 'regenerate'])
        ->name('equipment.qr-code.regenerate');
    Route::resource('equipment.inspections', InspectionController::class)
        ->shallow()
        ->only(['create', 'store', 'show']);
});

Route::middleware(['auth', EnsureWorkplaceUser::class])
    ->prefix('app')
    ->name('client.')
    ->group(function (): void {
        Route::get('/dashboard', ClientDashboardController::class)
            ->name('dashboard');

        Route::get('/equipment', [ClientEquipmentController::class, 'index'])
            ->name('equipment.index');

        Route::get('/equipment/{equipment}', [ClientEquipmentController::class, 'show'])
            ->name('equipment.show');
    });
