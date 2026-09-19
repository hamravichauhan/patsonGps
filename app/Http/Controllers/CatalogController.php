<?php

namespace App\Http\Controllers;

use App\Models\ProductCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CatalogController extends Controller
{
    /**
     * Helper to find all available angle images for any product image string
     */
    private function getProductImages($primaryImage)
    {
        $primaryImg = $primaryImage ?: 'default-product.webp';
        $baseName = pathinfo($primaryImg, PATHINFO_FILENAME);

        // Strip trailing digits/specials (e.g., 'kokum-squash00' -> 'kokum-squash')
        $cleanPrefix = preg_replace('/[0-9_\-\s]+$/', '', $baseName);
        if (empty($cleanPrefix) || strlen($cleanPrefix) < 3) {
            $cleanPrefix = $baseName;
        }

        $gallery = [];
        $imagePath = public_path('images/products');

        if (File::isDirectory($imagePath)) {
            $files = File::files($imagePath);
            foreach ($files as $file) {
                $filename = $file->getFilename();
                if (Str::startsWith($filename, $cleanPrefix) || Str::startsWith($filename, $baseName)) {
                    if (in_array(strtolower($file->getExtension()), ['webp', 'png', 'jpg', 'jpeg'])) {
                        $gallery[] = $filename;
                    }
                }
            }
        }

        // Ensure primary image is first
        if (!in_array($primaryImg, $gallery) && file_exists(public_path('images/products/' . $primaryImg))) {
            array_unshift($gallery, $primaryImg);
        }

        $gallery = array_values(array_unique($gallery));
        return !empty($gallery) ? $gallery : [$primaryImg];
    }

    public function index(Request $request)
    {
        $query = ProductCatalog::where('is_active', 1);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('is_featured', 'desc')
            ->orderBy('product_name')
            ->get()
            ->groupBy('slug');

        // Attach dynamic multi-image gallery array to each product group
        $productsWithGalleries = $products->map(function ($variants) {
            $primary = $variants->first();
            $variants->galleryImages = $this->getProductImages($primary->image);
            return $variants;
        });

        $categories = ProductCatalog::where('is_active', 1)
            ->distinct()
            ->pluck('category')
            ->filter();

        return view('catalog.index', [
            'products' => $productsWithGalleries,
            'categories' => $categories
        ]);
    }

    public function show($slug)
    {
        $product = ProductCatalog::available()->where('slug', $slug)->firstOrFail();
        $variants = ProductCatalog::available()->where('slug', $slug)->orderBy('price')->get();

        // 1. Start with the designated Cover Photo
        $gallery = [];
        if (!empty($product->image) && $product->image !== 'default-product.webp') {
            $gallery[] = basename($product->image);
        }

        // 2. Append ONLY the images checked and saved by the admin
        if (is_array($product->images)) {
            foreach ($product->images as $img) {
                $base = basename($img);
                if (!in_array($base, $gallery) && !empty($base) && $base !== 'default-product.webp') {
                    $gallery[] = $base;
                }
            }
        }

        if (empty($gallery)) {
            $gallery = ['default-product.webp'];
        }

        return view('catalog.show', [
            'product' => $product,
            'variants' => $variants,
            'galleryImages' => array_values($gallery),
        ]);
    }
}