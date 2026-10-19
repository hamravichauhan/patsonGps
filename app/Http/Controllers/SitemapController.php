<?php

namespace App\Http\Controllers;

use App\Models\ProductCatalog;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate a dynamic XML sitemap for search engines.
     * Includes static pages + all active product pages + category pages.
     */
    public function index(): Response
    {
        // ---------- Products (only active, distinct slugs) ----------
        $products = ProductCatalog::where('is_active', true)
            ->select('slug', 'product_name', 'image', 'created_at')
            ->orderBy('id', 'desc')
            ->get()
            ->unique('slug')
            ->values();

        // ---------- Categories (only active) ----------
        $categories = ProductCatalog::where('is_active', true)
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();

        // ---------- Static pages ----------
        $staticPages = [
            ['route' => 'home',          'priority' => '1.0', 'changefreq' => 'weekly'],
            ['route' => 'shop',          'priority' => '0.9', 'changefreq' => 'daily'],
            ['route' => 'about',         'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'contact',       'priority' => '0.7', 'changefreq' => 'monthly'],
        ];

        $content = view('sitemap', compact('products', 'categories', 'staticPages'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}