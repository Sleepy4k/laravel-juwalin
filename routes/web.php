<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Payment\PakasirWebhookController;
use App\Http\Controllers\Portal;
use App\Http\Controllers\PortForwardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public as PublicCtrl;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicCtrl\HomeController::class, 'index'])->name('home');
Route::get('/about', [PublicCtrl\HomeController::class, 'about'])->name('about');
Route::get('/products', [PublicCtrl\HomeController::class, 'products'])->name('products');
Route::get('/pricing', [PublicCtrl\HomeController::class, 'pricing'])->name('pricing');
Route::get('/contact', [PublicCtrl\ContactController::class, 'index'])->name('contact');
Route::post('/contact', [PublicCtrl\ContactController::class, 'send'])->name('contact.send');

/*
|--------------------------------------------------------------------------
| Payment Webhooks (no CSRF, rate-limited)
|--------------------------------------------------------------------------
*/
Route::post('/webhooks/pakasir', [PakasirWebhookController::class, 'handle'])
    ->name('webhooks.pakasir')
    ->middleware('throttle:webhook')
    ->withoutMiddleware([VerifyCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| Auth scaffolding (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Client portal
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('portal')->name('portal.')->group(static function(): void {
    Route::get('/dashboard', [Portal\DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Containers
    Route::get('/containers', [ContainerController::class, 'index'])->name('containers.index');
    Route::get('/containers/{container}', [ContainerController::class, 'show'])->name('containers.show');
    Route::get('/containers/{container}/status', [ContainerController::class, 'status'])->name('containers.status');
    Route::get('/containers/{container}/console', [ContainerController::class, 'console'])->name('containers.console');
    Route::get('/containers/{container}/vnc-url', [ContainerController::class, 'vncUrl'])->name('containers.vnc-url');
    Route::get('/containers/{container}/term-url', [ContainerController::class, 'termUrl'])->name('containers.term-url');
    Route::post('/containers/{container}/start', [ContainerController::class, 'start'])->name('containers.start');
    Route::post('/containers/{container}/stop', [ContainerController::class, 'stop'])->name('containers.stop');
    Route::post('/containers/{container}/restart', [ContainerController::class, 'restart'])->name('containers.restart');

    // Port forwarding
    Route::get('/containers/{container}/ports', [PortForwardingController::class, 'index'])->name('ports.index');
    Route::post('/containers/{container}/ports', [PortForwardingController::class, 'store'])->name('ports.store');
    Route::delete('/ports/{portForwardingRequest}', [PortForwardingController::class, 'destroy'])->name('ports.destroy');

    // Billing
    Route::get('/billing', [Portal\BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/{payment}', [Portal\BillingController::class, 'show'])->name('billing.show');
    Route::get('/billing/{payment}/invoice', [Portal\BillingController::class, 'invoice'])->name('billing.invoice');
    Route::post('/billing/{payment}/pay', [Portal\BillingController::class, 'pay'])->name('billing.pay');
});

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(static function(): void {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Packages CRUD
    Route::resource('packages', Admin\PackageController::class);

    // Orders management
    Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::delete('/orders/{order}', [Admin\OrderController::class, 'destroy'])->name('orders.destroy');

    // Users management
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create-admin', [Admin\UserController::class, 'createAdmin'])->name('users.create-admin');
    Route::post('/users/create-admin', [Admin\UserController::class, 'storeAdmin'])->name('users.store-admin');
    Route::get('/users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/role', [Admin\UserController::class, 'updateRole'])->name('users.role');
    Route::delete('/users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Containers management
    Route::get('/containers', [Admin\ContainerController::class, 'index'])->name('containers.index');
    Route::get('/containers/{container}', [Admin\ContainerController::class, 'show'])->name('containers.show');
    Route::get('/containers/{container}/status', [Admin\ContainerController::class, 'status'])->name('containers.status');
    Route::get('/containers/{container}/console', [Admin\ContainerController::class, 'console'])->name('containers.console');
    Route::get('/containers/{container}/vnc-url', [Admin\ContainerController::class, 'vncUrl'])->name('containers.vnc-url');
    Route::get('/containers/{container}/term-url', [Admin\ContainerController::class, 'termUrl'])->name('containers.term-url');
    Route::post('/containers/{container}/action', [Admin\ContainerController::class, 'action'])->name('containers.action');
    Route::delete('/containers/{container}', [Admin\ContainerController::class, 'destroy'])->name('containers.destroy');

    // Payments management
    Route::get('/payments', [Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [Admin\PaymentController::class, 'show'])->name('payments.show');
    Route::patch('/payments/{payment}/confirm', [Admin\PaymentController::class, 'confirm'])->name('payments.confirm');
    Route::patch('/payments/{payment}/reject', [Admin\PaymentController::class, 'reject'])->name('payments.reject');

    // Settings
    Route::get('/settings', [Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');

    // Activity logs
    Route::get('/activity-logs', [Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
});
