<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductCatalog;
use App\Models\Setting;
use App\Models\User;
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

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,dispatched,delivered,rejected',
            'admin_notes' => 'nullable|string'
        ]);

        $order = Order::findOrFail($id);
        $order->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes
        ]);

        return back()->with('success', "Order #{$order->order_number} status updated to " . strtoupper($request->status));
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
        $liveCount = ProductCatalog::where('is_active', 1)->distinct('slug')->count('slug');
        $hiddenCount = ProductCatalog::where('is_active', 0)->distinct('slug')->count('slug');

        return view('admin.products.index', compact(
            'groupedProducts',
            'categories',
            'totalCount',
            'liveCount',
            'hiddenCount',
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

        if ($request->hasFile('primary_image')) {
            $primaryImage = basename($request->file('primary_image')->store('products', 'public'));
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $galleryImages[] = basename($file->store('products', 'public'));
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
            'primary_image' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif|max:20480',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif|max:20480',
            'visible_images' => 'nullable|array',
            'primary_choice' => 'nullable|string',
        ]);

        // Gallery and cover image processing
        $selectedGallery = $request->input('visible_images', []);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $selectedGallery[] = basename($file->store('products', 'public'));
            }
        }

        $primaryImage = null;
        if ($request->hasFile('primary_image')) {
            $primaryImage = basename($request->file('primary_image')->store('products', 'public'));
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
        return back()->with('success', "Product {$product->product_name} is now {$statusLabel}.");
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

            return response()->json([
                'status' => 'success',
                'qrcode' => $data['qrcode'] ?? null,
                'state' => $data['status'] ?? ($data['connected'] ?? false ? 'CONNECTED' : 'DISCONNECTED')
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}