<?php

namespace App\Http\Controllers;

use App\Models\ProductCatalog;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        // Fetch featured flagship items for the home page
        $featuredProducts = ProductCatalog::available()
            ->where('is_featured', 1)
            ->get()
            ->groupBy('slug');

        $categories = ProductCatalog::available()
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->map(fn($c) => is_array($c) ? ($c['name'] ?? $c['slug'] ?? reset($c)) : (string) $c)
            ->unique()
            ->values();

        return view('pages.home', compact('featuredProducts', 'categories'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }
}