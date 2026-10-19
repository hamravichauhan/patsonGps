<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductCatalog;
use App\Models\Setting;
use App\Models\User;
use App\Services\ImageService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    // 1. Dashboard Metrics & Recent Quotes
    public function dashboard()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_revenue' => Order::where('status', '!=', 'rejected')->sum('total_amount'),
            'total_products' => ProductCatalog::count(),
            'total_users' => User::count(),
        ];

        $recentOrders = Order::with('items')->latest()->take(8)->get();
        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }

    // 2. Orders & Quotations Management
    public function orders(Request $request)
    {
        $query = Order::with('items')->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $orders = $query->paginate(15)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function updateOrderStatus(Request $request, $id, WhatsAppService $waService)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,dispatched,delivered,rejected',
            'admin_notes' => 'nullable|string'
        ]);

        $order = Order::with('items')->findOrFail($id);
        $oldStatus = $order->status;
        $order->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes
        ]);

        // Background WhatsApp automated notification when status changes
        if ($oldStatus !== $request->status) {
            $statusEmoji = [
                'approved' => '✅',
                'dispatched' => '🚚',
                'delivered' => '🎉',
                'rejected' => '❌',
                'pending' => '⏳'
            ][$request->status] ?? '📋';

            $statusText = [
                'approved' => 'Approved & Preparing for Dispatch',
                'dispatched' => 'Dispatched from Morjim Factory',
                'delivered' => 'Successfully Delivered',
                'rejected' => 'Order Declined / Cancelled',
                'pending' => 'Pending Verification'
            ][$request->status] ?? ucfirst($request->status);

            $msg = "🌿 *PatSons Foods Goa - Order Update*\n"
                . "━━━━━━━━━━━━━━━━━━━━\n"
                . "Hello *{$order->customer_name}*,\n\n"
                . "Your Order *#{$order->order_number}* status has been updated to:\n"
                . "{$statusEmoji} *{$statusText}*\n\n"
                . "📍 *Delivery Location:* {$order->delivery_address}\n"
                . "💰 *Total Value:* ₹" . number_format($order->total_amount, 2) . "\n"
                . ($request->admin_notes ? "📝 *Factory Note:* {$request->admin_notes}\n" : "")
                . "━━━━━━━━━━━━━━━━━━━━\n"
                . "Thank you for partnering with Dev Can Fruit Products, Morjim - Goa.";

            $waService->queueMessage($order->customer_phone, $msg);
        }

        return back()->with('success', "Order #{$order->order_number} status updated to " . strtoupper($request->status) . " (WhatsApp alert queued).");
    }

    public function printInvoice($id)
    {
        $order = Order::with('items')->findOrFail($id);
        return view('admin.orders.invoice', compact('order'));
    }

    // 3. Product Catalog Grouped by Base Product
    public function products(Request $request)
    {
        if ($request->has('reset')) {
            session()->forget([
                'admin_product_search',
                'admin_product_category',
                'admin_product_status_filter'
            ]);
            return redirect()->route('admin.products');
        }

        if ($request->has('search')) {
            session(['admin_product_search' => $request->search]);
        }
        if ($request->has('category')) {
            session(['admin_product_category' => $request->category]);
        }
        if ($request->has('status_filter')) {
            session(['admin_product_status_filter' => $request->status_filter]);
        }

        $search = session('admin_product_search', $request->get('search', ''));
        $category = session('admin_product_category', $request->get('category', ''));
        $statusFilter = session('admin_product_status_filter', $request->get('status_filter', ''));

        $query = ProductCatalog::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('size', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($category)) {
            $query->where('category', $category);
        }

        if (!empty($statusFilter)) {
            if ($statusFilter === 'active') {
                $query->where('is_active', 1);
            } elseif ($statusFilter === 'hidden') {
                $query->where('is_active', 0);
            }
        }

        $allMatching = $query->orderBy('product_name')->get();
        $groupedProducts = $allMatching->groupBy('slug');

        $categories = ProductCatalog::distinct()->pluck('category')->filter();
        $totalCount = ProductCatalog::distinct('slug')->count('slug');
        $totalSkus = ProductCatalog::count();
        $liveCount = ProductCatalog::where('is_active', 1)->distinct('slug')->count('slug');
        $hiddenCount = ProductCatalog::where('is_active', 0)->distinct('slug')->count('slug');
        $inStockCount = ProductCatalog::where(function ($q) {
            $q->whereNull('in_stock')->orWhere('in_stock', 1);
        })->distinct('slug')->count('slug');
        $outOfStockCount = ProductCatalog::where('in_stock', 0)->distinct('slug')->count('slug');
        $featuredCount = ProductCatalog::where('is_featured', 1)->distinct('slug')->count('slug');

        return view('admin.products.index', compact(
            'groupedProducts',
            'categories',
            'totalCount',
            'totalSkus',
            'liveCount',
            'hiddenCount',
            'inStockCount',
            'outOfStockCount',
            'featuredCount',
            'search',
            'category',
            'statusFilter'
        ));
    }

    public function createProduct()
    {
        $categories = ProductCatalog::distinct()->pluck('category')->filter();
        return view('admin.products.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'size' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'units_per_box' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'auto_approve_override' => 'nullable|boolean',
            'auto_approve_min_qty' => 'nullable|integer|min:1',
            'primary_image' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif,gif,heic|max:20480',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif,gif,heic|max:20480',
        ]);

        $slug = Str::slug($validated['product_name']);
        $primaryImage = 'default-product.webp';
        $galleryImages = [];

        $primaryFile = $request->file('primary_image') ?: $request->file('image_file');
        if ($primaryFile) {
            $primaryImage = ImageService::uploadAsWebp($primaryFile, 'products', 82, $slug . '-primary', true);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $galleryImages[] = ImageService::uploadAsWebp($file, 'products', 82, $slug . '-gallery', true);
            }
        }

        if ($primaryImage === 'default-product.webp' && !empty($galleryImages)) {
            $primaryImage = $galleryImages[0];
        }

        ProductCatalog::create([
            'product_name' => $validated['product_name'],
            'slug' => $slug,
            'category' => $validated['category'],
            'size' => $validated['size'],
            'price' => $validated['price'],
            'units_per_box' => (int) $validated['units_per_box'],
            'description' => $validated['description'] ?? null,
            'image' => $primaryImage,
            'images' => array_values(array_unique($galleryImages)),
            'is_featured' => $request->has('is_featured'),
            'is_active' => $request->has('is_active'),
            'auto_approve_override' => $request->has('auto_approve_override'),
            'auto_approve_min_qty' => $request->filled('auto_approve_min_qty') ? (int) $request->auto_approve_min_qty : null,
        ]);

        return redirect()->route('admin.products')->with('success', 'Product variant added to catalog!');
    }

    public function editProduct($id)
    {
        $product = ProductCatalog::findOrFail($id);
        $slug = $product->slug;

        $variants = ProductCatalog::where('slug', $slug)->orderBy('price')->get();
        $categories = ProductCatalog::distinct()->pluck('category')->filter();

        $assignedImages = [];
        if (!empty($product->image) && $product->image !== 'default-product.webp') {
            $assignedImages[] = basename($product->image);
        }
        if (is_array($product->images)) {
            foreach ($product->images as $img) {
                $base = basename($img);
                if (!in_array($base, $assignedImages)) {
                    $assignedImages[] = $base;
                }
            }
        }

        $cleanSlug = str_replace(['-', '_'], '', strtolower($slug));
        $allFoundImages = $assignedImages;

        $searchDirs = [
            public_path('images/products'),
            public_path('images'),
            public_path('storage/products'),
            public_path('storage'),
        ];

        foreach ($searchDirs as $dirPath) {
            if (is_dir($dirPath)) {
                $files = scandir($dirPath);
                foreach ($files as $file) {
                    if (in_array($file, ['.', '..', '.DS_Store', 'default-product.webp'])) {
                        continue;
                    }

                    $fileClean = str_replace(['-', '_'], '', strtolower(pathinfo($file, PATHINFO_FILENAME)));

                    if (str_starts_with($fileClean, substr($cleanSlug, 0, 7)) || str_contains($fileClean, substr($cleanSlug, 0, 7))) {
                        if (!in_array($file, $allFoundImages)) {
                            $allFoundImages[] = $file;
                        }
                    }
                }
            }
        }

        return view('admin.products.edit', compact('product', 'variants', 'categories', 'allFoundImages', 'assignedImages'));
    }

    public function updateProduct(Request $request, $id)
    {
        $mainProduct = ProductCatalog::findOrFail($id);
        $oldSlug = $mainProduct->slug;

        $request->validate([
            'product_name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'auto_approve_override' => 'nullable|boolean',
            'auto_approve_min_qty' => 'nullable|integer|min:1',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string|max:100',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.units_per_box' => 'required|integer|min:1',
            'primary_image' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif,gif,bmp,heic|max:20480',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif,gif,bmp,heic|max:20480',
            'visible_images' => 'nullable|array',
            'primary_choice' => 'nullable|string',
        ]);

        $newSlug = Str::slug($request->product_name);

        // Gallery and cover image processing
        $selectedGallery = $request->input('visible_images', []);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $selectedGallery[] = ImageService::uploadAsWebp($file, 'products', 82, $newSlug . '-gallery', true);
            }
        }

        $primaryImage = null;
        if ($request->hasFile('primary_image')) {
            $primaryImage = ImageService::uploadAsWebp($request->file('primary_image'), 'products', 82, $newSlug . '-primary', true);
        } elseif ($request->filled('primary_choice')) {
            $primaryImage = basename($request->primary_choice);
        }

        if (empty($primaryImage) && !empty($selectedGallery)) {
            $primaryImage = basename(reset($selectedGallery));
        }

        $cleanGallery = array_values(array_unique(array_map(fn($img) => basename($img), $selectedGallery)));
        $newSlug = Str::slug($request->product_name);
        $autoApproveOverride = $request->has('auto_approve_override');
        $autoApproveMinQty = $request->filled('auto_approve_min_qty') ? (int) $request->auto_approve_min_qty : null;
        $isFeatured = $request->has('is_featured');
        $resolvedImage = $primaryImage ?: ($mainProduct->image ?: 'default-product.webp');

        // Update all existing variants matching the old slug
        ProductCatalog::where('slug', $oldSlug)->update([
            'product_name' => $request->product_name,
            'slug' => $newSlug,
            'category' => $request->category,
            'description' => $request->description,
            'image' => $resolvedImage,
            'images' => $cleanGallery,
            'is_featured' => $isFeatured,
            'auto_approve_override' => $autoApproveOverride,
            'auto_approve_min_qty' => $autoApproveMinQty,
        ]);

        // Update existing variant rows or create newly added size variants
        foreach ($request->input('variants') as $vData) {
            if (!empty($vData['id'])) {
                $vModel = ProductCatalog::find($vData['id']);
                if ($vModel) {
                    $vModel->update([
                        'size' => $vData['size'],
                        'price' => $vData['price'],
                        'units_per_box' => (int) ($vData['units_per_box'] ?? 12),
                        'is_active' => isset($vData['is_active']) ? (bool) $vData['is_active'] : true,
                    ]);
                }
            } else {
                ProductCatalog::create([
                    'product_name' => $request->product_name,
                    'slug' => $newSlug,
                    'category' => $request->category,
                    'size' => $vData['size'],
                    'price' => $vData['price'],
                    'units_per_box' => (int) ($vData['units_per_box'] ?? 12),
                    'description' => $request->description,
                    'image' => $resolvedImage,
                    'images' => $cleanGallery,
                    'is_featured' => $isFeatured,
                    'is_active' => true,
                    'auto_approve_override' => $autoApproveOverride,
                    'auto_approve_min_qty' => $autoApproveMinQty,
                ]);
            }
        }

        return redirect()->route('admin.products')
            ->with('success', "Updated '{$request->product_name}' packaging and box rates successfully.");
    }

    public function toggleProductStatus($id)
    {
        $product = ProductCatalog::findOrFail($id);
        $newStatus = !$product->is_active;

        ProductCatalog::where('slug', $product->slug)->update([
            'is_active' => $newStatus
        ]);

        $statusLabel = $newStatus ? 'Visible in Store' : 'Hidden from Store';

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => (bool) $newStatus,
                'message' => "Product '{$product->product_name}' is now {$statusLabel}."
            ]);
        }

        return back()->with('success', "Product {$product->product_name} is now {$statusLabel}.");
    }

    public function toggleProductStock($id)
    {
        $product = ProductCatalog::findOrFail($id);
        $newStock = !($product->in_stock ?? true);

        ProductCatalog::where('slug', $product->slug)->update([
            'in_stock' => $newStock
        ]);

        $label = $newStock ? 'Marked In Stock' : 'Marked Out of Stock';

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'in_stock' => (bool) $newStock,
                'message' => "Product '{$product->product_name}' {$label}."
            ]);
        }

        return back()->with('success', "{$product->product_name} {$label}.");
    }

    public function toggleProductFeatured($id)
    {
        $product = ProductCatalog::findOrFail($id);
        $newFeatured = !$product->is_featured;

        ProductCatalog::where('slug', $product->slug)->update([
            'is_featured' => $newFeatured
        ]);

        $label = $newFeatured ? 'Marked as Featured / Best Seller' : 'Removed from Featured';

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_featured' => (bool) $newFeatured,
                'message' => "Product '{$product->product_name}' {$label}."
            ]);
        }

        return back()->with('success', "{$product->product_name} {$label}.");
    }

    public function quickUpdateVariant(Request $request, $id)
    {
        $variant = ProductCatalog::findOrFail($id);
        $request->validate([
            'price' => 'nullable|numeric|min:0',
            'units_per_box' => 'nullable|integer|min:1',
        ]);

        if ($request->filled('price')) {
            $variant->price = (float) $request->price;
        }
        if ($request->filled('units_per_box')) {
            $variant->units_per_box = (int) $request->units_per_box;
        }
        $variant->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'price' => number_format($variant->price, 2),
                'units_per_box' => $variant->units_per_box,
                'box_price' => number_format($variant->box_price, 2),
                'message' => "Updated {$variant->size} variant (₹{$variant->price}) successfully."
            ]);
        }

        return back()->with('success', "Updated {$variant->size} variant successfully.");
    }

    public function duplicateProduct($id)
    {
        $product = ProductCatalog::findOrFail($id);
        $baseName = preg_replace('/\s*\(Copy.*?\)$/', '', $product->product_name);
        $newName = $baseName . ' (Copy)';
        $newSlug = Str::slug($newName . '-' . Str::random(4));

        $newProduct = $product->replicate();
        $newProduct->product_name = $newName;
        $newProduct->slug = $newSlug;
        $newProduct->is_active = false; // Start draft
        $newProduct->save();

        return redirect()->route('admin.products.edit', $newProduct->id)
            ->with('success', "Duplicated '{$product->product_name}' to '{$newName}'. Customize pricing and details below.");
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:activate,deactivate,mark_in_stock,mark_out_of_stock,feature,unfeature,delete',
            'slugs' => 'required|array|min:1',
            'slugs.*' => 'string'
        ]);

        $slugs = $request->input('slugs');
        $action = $request->input('action');
        $count = count($slugs);

        switch ($action) {
            case 'activate':
                ProductCatalog::whereIn('slug', $slugs)->update(['is_active' => true]);
                $msg = "{$count} products activated and live in shop.";
                break;
            case 'deactivate':
                ProductCatalog::whereIn('slug', $slugs)->update(['is_active' => false]);
                $msg = "{$count} products hidden from shop.";
                break;
            case 'mark_in_stock':
                ProductCatalog::whereIn('slug', $slugs)->update(['in_stock' => true]);
                $msg = "{$count} products marked In Stock.";
                break;
            case 'mark_out_of_stock':
                ProductCatalog::whereIn('slug', $slugs)->update(['in_stock' => false]);
                $msg = "{$count} products marked Out of Stock.";
                break;
            case 'feature':
                ProductCatalog::whereIn('slug', $slugs)->update(['is_featured' => true]);
                $msg = "{$count} products marked as Featured / Best Seller.";
                break;
            case 'unfeature':
                ProductCatalog::whereIn('slug', $slugs)->update(['is_featured' => false]);
                $msg = "{$count} products removed from Featured.";
                break;
            case 'delete':
                ProductCatalog::whereIn('slug', $slugs)->delete();
                $msg = "{$count} products removed from catalog.";
                break;
            default:
                $msg = "Bulk action completed.";
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }

    public function exportCatalog()
    {
        $products = ProductCatalog::orderBy('category')->orderBy('product_name')->orderBy('price')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="patsons-catalog-' . date('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Microsoft Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'SKU ID',
                'Product Name',
                'Category',
                'Pack Size',
                'Unit Price (INR)',
                'Units Per Box',
                'Box Price (INR)',
                'Live In Store',
                'Stock Status',
                'Featured Best Seller',
                'Vyapar Item ID',
                'Image File',
                'Storefront URL'
            ], ',', '"', '\\');

            foreach ($products as $p) {
                fputcsv($file, [
                    $p->id,
                    $p->product_name,
                    $p->category,
                    $p->size,
                    number_format($p->price, 2, '.', ''),
                    $p->units_per_box ?: 12,
                    number_format($p->box_price, 2, '.', ''),
                    $p->is_active ? 'LIVE' : 'HIDDEN',
                    ($p->in_stock ?? true) ? 'IN_STOCK' : 'OUT_OF_STOCK',
                    $p->is_featured ? 'YES' : 'NO',
                    $p->vyapar_item_id ?? '',
                    $p->image ?? '',
                    url('/products/' . $p->slug),
                ], ',', '"', '\\');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function deleteProductImage(Request $request, $id)
    {
        $product = ProductCatalog::findOrFail($id);
        $imageToDelete = basename($request->input('image_path'));

        $currentImages = is_array($product->images) ? $product->images : [];
        $updatedImages = array_filter($currentImages, function ($img) use ($imageToDelete) {
            return basename($img) !== $imageToDelete;
        });

        if (basename($product->image) === $imageToDelete) {
            $product->image = !empty($updatedImages) ? reset($updatedImages) : 'default-product.webp';
        }

        $product->images = array_values($updatedImages);
        $product->save();

        if (Storage::disk('public')->exists('products/' . $imageToDelete)) {
            Storage::disk('public')->delete('products/' . $imageToDelete);
        }

        return back()->with('success', 'Image removed from gallery.');
    }

    public function quickUpdateImage(Request $request, $id)
    {
        $product = ProductCatalog::findOrFail($id);
        $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,webp,avif,gif,bmp,heic|max:20480'
        ]);

        $webpName = ImageService::uploadAsWebp($request->file('image'), 'products', 82, $product->slug . '-quick', true);
        $product->image = $webpName;
        $product->save();

        ProductCatalog::where('slug', $product->slug)->update(['image' => $webpName]);

        return back()->with('success', 'Product image updated to ' . $webpName);
    }

    public function resetToDefaultImage($id)
    {
        $product = ProductCatalog::findOrFail($id);
        ProductCatalog::where('slug', $product->slug)->update(['image' => 'default-product.webp']);
        return back()->with('success', 'Product image reset to default.');
    }

    public function updateImageTag(Request $request, $id)
    {
        $product = ProductCatalog::findOrFail($id);
        $request->validate([
            'tag' => 'required|string|in:auto,matched,mismatched,default'
        ]);
        ProductCatalog::where('slug', $product->slug)->update(['image_status' => $request->tag]);
        return back()->with('success', 'Image status tag updated.');
    }

    public function deleteProduct($id)
    {
        $product = ProductCatalog::findOrFail($id);

        if ($product->image && $product->image !== 'default-product.webp' && Storage::disk('public')->exists('products/' . basename($product->image))) {
            Storage::disk('public')->delete('products/' . basename($product->image));
        }
        if (is_array($product->images)) {
            foreach ($product->images as $img) {
                $base = basename($img);
                if (Storage::disk('public')->exists('products/' . $base)) {
                    Storage::disk('public')->delete('products/' . $base);
                }
            }
        }

        ProductCatalog::where('slug', $product->slug)->delete();

        return back()->with('success', 'Product and all its variants deleted.');
    }

    // 4. Users Directory
    public function users()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    // 5. Store Settings
    public function settings()
    {
        $adminUser = auth()->user();
        $adminPhone = Setting::get('whatsapp_admin_number', config('services.whatsapp.admin_number', '917758943614'));
        $minAutoQty = Setting::get('auto_approval_min_qty', 10);
        $autoApproveEnabled = Setting::get('auto_approval_enabled', 1);

        $viewName = view()->exists('admin.settings.index') ? 'admin.settings.index' : 'admin.settings';

        return view($viewName, compact('adminUser', 'adminPhone', 'minAutoQty', 'autoApproveEnabled'));
    }

    public function updateSettings(Request $request)
    {
        $admin = auth()->user();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'whatsapp_admin_number' => 'required|regex:/^[0-9]{10,15}$/',
            'auto_approval_min_qty' => 'required|integer|min:1',
            'auto_approval_enabled' => 'nullable|boolean',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => ['nullable', 'confirmed', Password::min(6)],
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $admin->password)) {
                return back()->withErrors(['current_password' => 'The provided current password does not match your account.']);
            }
            $admin->password = Hash::make($request->new_password);
        }

        $admin->save();

        $cleanPhone = preg_replace('/[^0-9]/', '', $request->whatsapp_admin_number);
        Setting::set('whatsapp_admin_number', $cleanPhone);
        Setting::set('auto_approval_min_qty', (int) $request->auto_approval_min_qty);
        Setting::set('auto_approval_enabled', $request->has('auto_approval_enabled') ? 1 : 0);

        return back()->with('success', 'Admin profile, credentials, and store settings updated successfully!');
    }

    // 6. Live WhatsApp QR Code Link Endpoint
    public function getWhatsAppQr()
    {
        $token = config('services.whatsapp.secret_token', env('WHATSAPP_SECRET_TOKEN'));
        $apiUrl = config('services.whatsapp.api_url', env('WHATSAPP_API_URL', 'http://127.0.0.1:21465/api/patsons-session'));

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->timeout(10)->post("{$apiUrl}/start-session", [
                        'waitQrCode' => true
                    ]);

            $data = $response->json();
            $connected = ($data['connected'] ?? false) || in_array($data['state'] ?? '', ['CONNECTED', 'inChat', 'isLogged', 'chatsAvailable', 'qrReadSuccess']);

            return response()->json([
                'status' => 'success',
                'connected' => (bool) $connected,
                'qrcode' => $connected ? null : ($data['qrcode'] ?? null),
                'state' => $connected ? 'CONNECTED' : ($data['state'] ?? 'INITIALIZING')
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}