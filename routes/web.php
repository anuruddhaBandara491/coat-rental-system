<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

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

Route::controller(AuthController::class)->name('auth.')->group(function () {
    Route::get('/', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::post('/dashboard/toggle-metrics', [DashboardController::class, 'toggleMetrics'])->name('dashboard.toggle-metrics');

    Route::controller(\App\Http\Controllers\OrderController::class)->prefix('order/')->name('order.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('view/{id}', 'view')->name('view');
        Route::get('destroy/{id}', 'destroy')->name('destroy');
        Route::post('/store', 'store')->name('store');
        Route::get('/search-coat/{type}', 'searchCoat')->name('search.coat');
        Route::get('/search-items', 'searchItems')->name('search.items');
        Route::get('check-availability', 'checkAvailability')->name('check-availability');
        Route::post('add-temp-orders', 'addTempOrders')->name('add-temp-orders');
        Route::get('get-temp-order-details', 'getTempOrderDetails')->name('get-temp-order-details ');
        Route::get('delete-all-temp-order', 'deleteTempOrder')->name('delete-all-temp-order');
        Route::get('/delete-item/{id}', 'deleteItem')->name('delete-item');
        Route::post('update-status', 'updateStatus')->name('update-status');
        Route::post('update-date/{id}', 'updateDate')->name('update-date');
        Route::post('/receipt/{id}', 'printOrder')->name('receipt');
        Route::delete('delete-order-item/{id}', 'deleteOrderItem')->name('delete-item'); // New route
        Route::post('add-orders', 'addOrderItem')->name('add-orders');

    });
    Route::controller(\App\Http\Controllers\ItemController::class)->prefix('item/')->name('item.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::get('/destroy/{id}', 'destroy')->name('destroy');
        Route::get('view/{id}', 'view')->name('view');
    });
    Route::prefix('customer')->controller(CustomerController::class)->name('customer.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::get('/destroy/{id}', 'destroy')->name('destroy');
    });

    Route::prefix('setting')->controller(\App\Http\Controllers\SettingController::class)->name('setting.')->group(function () {
        Route::get('/view-profile', 'viewProfile')->name('view-profile');
        Route::post('/update-password', 'updatePassword')->name('update-password');
        Route::post('/profile-update', 'profileUpdate')->name('profile-update');

    });
    Route::prefix('reports')->controller(ReportController::class)->name('reports.')->group(function () {
        Route::get('/due-list', 'DueList')->name('due-list');
        Route::get('/financial-report', 'financialReport')->name('financial');
        Route::get('/sale-report', 'saleReport')->name('sale');
        Route::get('/profit-report', 'profitReport')->name('profit');
        Route::get('/total-stock', 'totalStock')->name('stock');
    });
    Route::get('test-print/{id}', [\App\Http\Controllers\OrderController::class, 'receiptPrint'])->name('test-print');

});

require __DIR__.'/auth.php';
