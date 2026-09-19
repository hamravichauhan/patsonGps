<?php

namespace App\Http\Controllers;

use App\Models\ProductCatalog;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $products = ProductCatalog::where('is_active', true)
            ->distinct('slug')
            ->orderBy('updated_at', 'desc')
            ->get();

        $categories = ProductCatalog::where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->filter();

        $content = view('sitemap', compact('products', 'categories'))->render();

        return response($content, 200)->header('Content-Type', 'text/xml');
    }
}