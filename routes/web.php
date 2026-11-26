<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\SitemapController;

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

// 301 Permanent Redirects for SEO URL variations
Route::get('/products/{slug}', fn($slug) => redirect('/product/' . $slug, 301));
Route::get('/catalog/{slug}', fn($slug) => redirect('/product/' . $slug, 301));
Route::get('/item/{slug}', fn($slug) => redirect('/product/' . $slug, 301));
Route::get('/items/{slug}', fn($slug) => redirect('/product/' . $slug, 301));

// 3. Static Content Pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// 4. SEO - XML Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// 5. Multi-Language Switcher (EN, Konkani, Marathi, Hindi)
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
Route::post('/my-account/request-wholesale', [CustomerAccountController::class, 'requestWholesale'])->name('customer.request-wholesale');

// WhatsApp OTP Authentication for Tracking
Route::post('/track-order/send-otp', [CustomerAccountController::class, 'sendOtp'])->middleware('throttle:6,1')->name('customer.otp.send');
Route::post('/track-order/verify-otp', [CustomerAccountController::class, 'verifyOtp'])->middleware('throttle:6,1')->name('customer.otp.verify');

// Logout
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
Route::post('/order/send-otp', [OrderController::class, 'sendOtp'])->middleware('throttle:6,1')->name('order.sendOtp');
Route::post('/order/verify-otp', [OrderController::class, 'verifyOtp'])->middleware('throttle:6,1')->name('order.verifyOtp');

// Order Submission Endpoints (Rate limited to prevent automated spam)
Route::post('/order/submit', [OrderController::class, 'store'])->middleware('throttle:5,1')->name('order.store');
Route::post('/order/store', [OrderController::class, 'store'])->middleware('throttle:5,1');

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
    Route::get('/orders/{id}/invoice', [AdminController::class, 'printInvoice'])->name('orders.invoice');

    // Product Catalog CRUD & Store Visibility
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products/export', [AdminController::class, 'exportCatalog'])->name('products.export');
    Route::post('/products/bulk-action', [AdminController::class, 'bulkAction'])->name('products.bulkAction');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::post('/products/{id}/duplicate', [AdminController::class, 'duplicateProduct'])->name('products.duplicate');
    Route::get('/products/{id}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [AdminController::class, 'deleteProduct'])->name('products.delete');
    Route::delete('/products/{id}/delete-image', [AdminController::class, 'deleteProductImage'])->name('products.deleteImage');
    Route::patch('/products/{id}/toggle-status', [AdminController::class, 'toggleProductStatus'])->name('products.toggleStatus');
    Route::patch('/products/{id}/toggle-stock', [AdminController::class, 'toggleProductStock'])->name('products.toggleStock');
    Route::patch('/products/{id}/toggle-featured', [AdminController::class, 'toggleProductFeatured'])->name('products.toggleFeatured');
    Route::patch('/products/{id}/quick-update', [AdminController::class, 'quickUpdateVariant'])->name('products.quickUpdate');

    // Manual Image Status Tag Update Route
    Route::patch('/products/{id}/update-image-tag', [AdminController::class, 'updateImageTag'])->name('products.updateImageTag');

    // Inline Image Quick Swap & Reset
    Route::post('/products/{id}/quick-image', [AdminController::class, 'quickUpdateImage'])->name('products.quickImage');
    Route::post('/products/{id}/reset-image', [AdminController::class, 'resetToDefaultImage'])->name('products.resetImage');

    // Customer & User Directory
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{id}/toggle-wholesaler', [AdminController::class, 'toggleWholesaler'])->name('users.toggle-wholesaler');

    // Store Settings, Password & WhatsApp Automation
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::get('/whatsapp/qr', [AdminController::class, 'getWhatsAppQr'])->name('whatsapp.qr');
});

// 3. Block default /admin paths (Returns 404 to scanners)
if ($adminPrefix !== 'admin') {
    Route::any('/admin/{any?}', function () {
        abort(404);
    })->where('any', '.*');
}

/*
|--------------------------------------------------------------------------
| Dynamic Static Asset Fallback (Linux Case-Sensitivity & cPanel Auto-Healing)
|--------------------------------------------------------------------------
*/
Route::get('/images/{folder}/{filename}', function ($folder, $filename) {
    $searchFolders = [$folder, ucfirst($folder), strtolower($folder)];
    $paths = [];
    foreach ($searchFolders as $f) {
        $paths[] = public_path('images/' . $f . '/' . $filename);
        $paths[] = base_path('../../public_html/images/' . $f . '/' . $filename);
        $paths[] = base_path('../public_html/images/' . $f . '/' . $filename);
    }
    $paths[] = public_path('images/' . $filename);
    $paths[] = base_path('../../public_html/images/' . $filename);
    $paths[] = base_path('../public_html/images/' . $filename);

    foreach ($paths as $path) {
        if (file_exists($path) && is_file($path)) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mime = match ($ext) {
                'webp' => 'image/webp',
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'svg' => 'image/svg+xml',
                'gif' => 'image/gif',
                'ico' => 'image/x-icon',
                default => 'application/octet-stream',
            };
            return response()->file($path, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }
    }

    $fallback = public_path('images/Products/default-product.webp');
    if (file_exists($fallback)) {
        return response()->file($fallback, ['Content-Type' => 'image/webp']);
    }
    abort(404);
})->where('filename', '.*');

Route::get('/images/{filename}', function ($filename) {
    $paths = [
        public_path('images/' . $filename),
        public_path('images/Products/' . $filename),
        base_path('../../public_html/images/' . $filename),
        base_path('../public_html/images/' . $filename),
        base_path('../../public_html/images/Products/' . $filename),
        base_path('../public_html/images/Products/' . $filename),
    ];
    foreach ($paths as $path) {
        if (file_exists($path) && is_file($path)) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mime = match ($ext) {
                'webp' => 'image/webp',
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'svg' => 'image/svg+xml',
                'gif' => 'image/gif',
                default => 'application/octet-stream',
            };
            return response()->file($path, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }
    }
    abort(404);
});
