<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CustomerAccountController;

/*
|--------------------------------------------------------------------------
| Public Storefront & Informational Routes
|--------------------------------------------------------------------------
*/

// 1. Home Page
Route::get('/', [PageController::class, 'home'])->name('home');

// 2. Shop & Product Catalog Routes
Route::get('/shop', [CatalogController::class, 'index'])->name('shop');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/product/{slug}', [CatalogController::class, 'show'])->name('catalog.show');

// 3. Static Content Pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// 4. Multi-Language Switcher (EN, Konkani, Marathi, Hindi)
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'gom', 'mr', 'hi'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Customer Account, WhatsApp OTP & Order Tracking Routes
|--------------------------------------------------------------------------
*/

// Customer Account & Order Tracking Hub
Route::get('/track-order', [CustomerAccountController::class, 'index'])->name('customer.account');
Route::get('/my-account', [CustomerAccountController::class, 'index']);

// WhatsApp OTP Authentication for Tracking
Route::post('/track-order/send-otp', [CustomerAccountController::class, 'sendOtp'])->name('customer.otp.send');
Route::post('/track-order/verify-otp', [CustomerAccountController::class, 'verifyOtp'])->name('customer.otp.verify');

// Fallback direct login & logout
Route::post('/my-account/login', [CustomerAccountController::class, 'loginWithPhone'])->name('customer.login');
Route::post('/track-order/logout', [CustomerAccountController::class, 'logout'])->name('customer.logout');
Route::post('/my-account/logout', [CustomerAccountController::class, 'logout']);

// 1-Click Reorder Cart Retrieval
Route::get('/api/reorder/{orderNumber}', [CustomerAccountController::class, 'loadReorder'])->name('api.reorder');

/*
|--------------------------------------------------------------------------
| Cart Checkout & WhatsApp Quotation Order Routes
|--------------------------------------------------------------------------
*/

// OTP Verification Flow during Cart Drawer Checkout
Route::post('/order/send-otp', [OrderController::class, 'sendOtp'])->name('order.sendOtp');
Route::post('/order/verify-otp', [OrderController::class, 'verifyOtp'])->name('order.verifyOtp');

// Order Submission Endpoints
Route::post('/order/submit', [OrderController::class, 'store'])->name('order.store');
Route::post('/order/store', [OrderController::class, 'store']);

// Direct Token Reorder
Route::get('/reorder/{token}', [OrderController::class, 'reorderWithToken'])->name('order.reorder.token');
Route::get('/reorder', [OrderController::class, 'reorder'])->name('order.reorder');

/*
|--------------------------------------------------------------------------
| Custom Secret Admin Authentication & Dashboard
|--------------------------------------------------------------------------
*/

$adminPrefix = env('ADMIN_SECRET_PATH', 'ps-control-morjim');

// 1. Admin Authentication Routes
Route::get("/{$adminPrefix}/login", [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post("/{$adminPrefix}/login", [AdminAuthController::class, 'login'])->middleware('throttle:5,1')->name('admin.login.submit');
Route::post("/{$adminPrefix}/logout", [AdminAuthController::class, 'logout'])->name('logout');

// 2. Protected Admin Panel Group
Route::middleware(['auth', 'admin'])->prefix($adminPrefix)->name('admin.')->group(function () {
    // Metrics & Orders
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::patch('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');

    // Product Catalog CRUD & Store Visibility
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [AdminController::class, 'deleteProduct'])->name('products.delete');
    Route::delete('/products/{id}/delete-image', [AdminController::class, 'deleteProductImage'])->name('products.deleteImage');
    Route::patch('/products/{id}/toggle-status', [AdminController::class, 'toggleProductStatus'])->name('products.toggleStatus');

    // Manual Image Status Tag Update Route
    Route::patch('/products/{id}/update-image-tag', [AdminController::class, 'updateImageTag'])->name('products.updateImageTag');

    // Inline Image Quick Swap & Reset
    Route::post('/products/{id}/quick-image', [AdminController::class, 'quickUpdateImage'])->name('products.quickImage');
    Route::post('/products/{id}/reset-image', [AdminController::class, 'resetToDefaultImage'])->name('products.resetImage');

    // Customer & User Directory
    Route::get('/users', [AdminController::class, 'users'])->name('users');

    // Store Settings, Password & WhatsApp Automation
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});

// 3. Block default /admin paths (Returns 404 to scanners)
if ($adminPrefix !== 'admin') {
    Route::any('/admin/{any?}', function () {
        abort(404);
    })->where('any', '.*');
}

Route::get('/whatsapp/qr', [App\Http\Controllers\AdminController::class, 'getWhatsAppQr'])->name('admin.whatsapp.qr');

Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');