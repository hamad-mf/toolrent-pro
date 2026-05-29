<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Super Admin Routes
Route::middleware(['auth', 'role:super-admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/tenants/{tenant}/inspect', [App\Http\Controllers\SuperAdmin\TenantController::class, 'inspect'])->name('tenants.inspect');
    Route::resource('tenants', App\Http\Controllers\SuperAdmin\TenantController::class);
    Route::resource('users', App\Http\Controllers\SuperAdmin\UserController::class)->only(['index', 'destroy']);
    Route::get('/settings', [App\Http\Controllers\SuperAdmin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\SuperAdmin\SettingsController::class, 'update'])->name('settings.update');
});

// Shop Routes — all shop roles can reach the prefix; access is refined per-feature below.
Route::middleware(['auth', 'role:shop-admin,manager,counter-staff,floor-staff'])->prefix('shop')->name('shop-admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\ShopAdmin\DashboardController::class, 'index'])->name('dashboard');

    // Tools — every shop role can view the inventory list and individual tools.
    Route::middleware('feature:tools')->group(function () {
        Route::get('/tools', [App\Http\Controllers\ShopAdmin\ToolController::class, 'index'])->name('tools.index');
        Route::get('/tools/{tool}', [App\Http\Controllers\ShopAdmin\ToolController::class, 'show'])->name('tools.show')->whereNumber('tool');
    });

    // Floor Staff capability: toggle maintenance / update condition.
    Route::middleware(['role:shop-admin,manager,floor-staff', 'feature:tools'])->group(function () {
        Route::post('/tools/{tool}/maintenance', [App\Http\Controllers\ShopAdmin\ToolController::class, 'toggleMaintenance'])->name('tools.maintenance');
        Route::post('/tools/{tool}/condition', [App\Http\Controllers\ShopAdmin\ToolController::class, 'updateCondition'])->name('tools.condition');
    });

    // Inventory management (create/edit/delete tools, categories, QR codes) — shop-admin + manager.
    Route::middleware('role:shop-admin,manager')->group(function () {
        Route::middleware('feature:categories')->group(function () {
            Route::resource('categories', App\Http\Controllers\ShopAdmin\CategoryController::class)->except(['show']);
        });

        Route::get('/tools/{tool}/qrcode', [App\Http\Controllers\ShopAdmin\ToolController::class, 'qrcode'])
            ->middleware('feature:tools,qrcode')
            ->name('tools.qrcode');

        Route::resource('tools', App\Http\Controllers\ShopAdmin\ToolController::class)
            ->middleware('feature:tools')
            ->except(['index', 'show']);

        // Reports
        Route::get('/reports', [App\Http\Controllers\ShopAdmin\ReportController::class, 'index'])
            ->middleware('feature:reports')
            ->name('reports.index');
    });

    // Counter operations: customers + rental lifecycle — shop-admin, manager, counter-staff.
    Route::middleware('role:shop-admin,manager,counter-staff')->group(function () {
        Route::resource('customers', App\Http\Controllers\ShopAdmin\CustomerController::class)
            ->middleware('feature:customers');

        Route::middleware('feature:rentals')->group(function () {
            Route::get('/rentals', [App\Http\Controllers\ShopAdmin\RentalController::class, 'index'])->name('rentals.index');
            Route::get('/rentals/checkout', [App\Http\Controllers\ShopAdmin\RentalController::class, 'create'])->name('rentals.create');
            Route::post('/rentals/checkout', [App\Http\Controllers\ShopAdmin\RentalController::class, 'store'])->name('rentals.store');
            Route::get('/rentals/{rental}', [App\Http\Controllers\ShopAdmin\RentalController::class, 'show'])->name('rentals.show');
            Route::post('/rentals/{rental}/checkout', [App\Http\Controllers\ShopAdmin\RentalController::class, 'checkoutBooking'])->name('rentals.checkout');
            Route::post('/rentals/{rental}/return', [App\Http\Controllers\ShopAdmin\RentalController::class, 'returnTool'])->name('rentals.return');
            Route::get('/rentals/{rental}/invoice', [App\Http\Controllers\ShopAdmin\RentalController::class, 'invoice'])
                ->middleware('feature:invoicing')
                ->name('rentals.invoice');
        });
    });

    // Shop administration: staff management + white-label settings — shop-admin only.
    Route::middleware('role:shop-admin')->group(function () {
        Route::resource('staff', App\Http\Controllers\ShopAdmin\StaffController::class)->except(['show']);
        Route::get('/settings', [App\Http\Controllers\ShopAdmin\SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [App\Http\Controllers\ShopAdmin\SettingsController::class, 'update'])->name('settings.update');
    });
});
