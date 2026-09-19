<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle = trim($__env->yieldContent('meta_title')) ?: (trim($__env->yieldContent('title')) ?: 'PatSons Foods Goa - Authentic Goan Kokum, Cashew Jams, Syrups & Fruit Crushes');
        $pageDescription = trim($__env->yieldContent('meta_description')) ?: 'Authentic Goan Kokum Agal, handcrafted Cashew Fruit Jams, herbal digestifs, cocktail cordials, and refreshing squashes direct from Dev Can Fruit Products, Morjim, Pernem - Goa.';
        $pageKeywords = trim($__env->yieldContent('meta_keywords')) ?: 'PatSons Foods Goa, Dev Can Fruit Products, Goan Kokum Agal, Cashew Jam Goa, Kokum Syrup, Pachak Appetizer, Bar Syrups Goa, Fruit Crushes Goa, Morjim Factory';
        $adminPhone = \App\Models\Setting::get('whatsapp_admin_number', config('services.whatsapp.admin_number', '917758943614'));
        $logoUrl = file_exists(public_path('images/LOGO.webp'))
            ? asset('images/LOGO.webp')
            : (file_exists(public_path('images/products/LOGO.webp'))
                ? asset('images/products/LOGO.webp')
                : asset('images/default-product.webp'));
        $ogImage = trim($__env->yieldContent('og_image')) ?: $logoUrl;
        $searchRoute = \Illuminate\Support\Facades\Route::has('api.products.search') ? route('api.products.search') : url('/shop');

        $globalSchema = [
            "@context" => "https://schema.org",
            "@graph" => [
                [
                    "@type" => "LocalBusiness",
                    "@id" => url('/') . "/#organization",
                    "name" => "PatSons Foods (Dev Can Fruit Products)",
                    "alternateName" => "PatSons Goa",
                    "url" => url('/'),
                    "logo" => [
                        "@type" => "ImageObject",
                        "url" => $logoUrl
                    ],
                    "image" => $logoUrl,
                    "description" => "Manufacturer and supplier of authentic Goan Kokum Agal, Cashew Fruit Jams, Fruit Crushes, and Cordials in Morjim, Pernem - Goa.",
                    "telephone" => "+" . $adminPhone,
                    "priceRange" => "₹₹",
                    "address" => [
                        "@type" => "PostalAddress",
                        "streetAddress" => "Dev Can Fruit Products, Morjim",
                        "addressLocality" => "Pernem",
                        "addressRegion" => "Goa",
                        "postalCode" => "403512",
                        "addressCountry" => "IN"
                    ],
                    "geo" => [
                        "@type" => "GeoCoordinates",
                        "latitude" => "15.6322",
                        "longitude" => "73.7438"
                    ],
                    "contactPoint" => [
                        "@type" => "ContactPoint",
                        "telephone" => "+" . $adminPhone,
                        "contactType" => "Customer Service and Wholesale Orders",
                        "areaServed" => "IN",
                        "availableLanguage" => ["English", "Hindi", "Konkani", "Marathi"]
                    ]
                ],
                [
                    "@type" => "WebSite",
                    "@id" => url('/') . "/#website",
                    "url" => url('/'),
                    "name" => "PatSons Foods Goa",
                    "publisher" => [
                        "@id" => url('/') . "/#organization"
                    ],
                    "potentialAction" => [
                        "@type" => "SearchAction",
                        "target" => route('shop') . "?search={search_term_string}",
                        "query-input" => "required name=search_term_string"
                    ]
                ]
            ]
        ];
    @endphp

    <!-- Primary SEO Meta Tags -->
    <title>{{ $pageTitle }}</title>
    <meta name="title" content="{{ $pageTitle }}">
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $pageKeywords }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="Dev Can Fruit Products (PatSons Foods Goa)">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Social Meta -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="PatSons Foods Goa">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:locale" content="en_IN">

    <!-- Twitter Card Meta -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <!-- Favicon -->
    <link rel="icon" type="image/webp" href="{{ $logoUrl }}">
    <link rel="apple-touch-icon" href="{{ $logoUrl }}">

    <!-- Preconnect CDNs -->
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://unpkg.com">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .safe-area-inset-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .toast-success {
            background: #10b981;
            color: white;
        }

        .toast-error {
            background: #ef4444;
            color: white;
        }
    </style>

    <!-- Global JSON-LD -->
    <script type="application/ld+json">
    {!! json_encode($globalSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>

    @stack('schema')
</head>

<body class="bg-stone-50 text-gray-900 min-h-screen flex flex-col font-sans antialiased pb-20 sm:pb-0"
    x-data="cartManager()"
    x-on:add-to-cart.window="addToCart($event.detail.id, $event.detail.name, $event.detail.price, $event.detail.size, $event.detail.image)">

    <!-- Top Utility Bar -->
    <div class="bg-green-950 text-white text-[11px] py-2 px-4 border-b border-green-900">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span class="font-bold text-amber-200">🌴 Dev Can Fruit Products • Morjim, Pernem - Goa</span>
            </div>

            <div class="hidden sm:flex items-center gap-4 font-bold text-green-200">
                <a href="https://wa.me/{{ $adminPhone }}" target="_blank" rel="noopener noreferrer"
                    class="hover:text-white transition flex items-center gap-1">
                    <span>💬 Direct Order:</span>
                    <span class="text-white font-mono">+{{ $adminPhone }}</span>
                </a>


            </div>
        </div>
    </div>

    <!-- Main Navbar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-xs"
        x-data="{ mobileMenuOpen: false, searchOpen: false, searchQuery: '', searchResults: [] }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 sm:h-24">

                <!-- Brand Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center py-1 group"
                        aria-label="PatSons Foods Goa Home">
                        <img src="{{ $logoUrl }}" alt="PatSons Goa Official Logo"
                            class="h-14 sm:h-20 w-auto max-w-[220px] sm:max-w-[320px] object-contain transition-transform duration-300 group-hover:scale-105 drop-shadow-md">
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-7 text-xs font-black uppercase tracking-wider">
                    <a href="{{ route('home') }}"
                        class="transition py-1 {{ request()->routeIs('home') ? 'text-green-900 border-b-2 border-green-900' : 'text-gray-600 hover:text-green-900' }}">
                        Home
                    </a>
                    <a href="{{ route('shop') }}"
                        class="transition py-1 {{ request()->routeIs('shop') || request()->routeIs('catalog.*') ? 'text-green-900 border-b-2 border-green-900' : 'text-gray-600 hover:text-green-900' }}">
                        Catalog & Shop
                    </a>
                    <a href="{{ route('customer.account') }}"
                        class="transition py-1 {{ request()->routeIs('customer.account') ? 'text-green-900 border-b-2 border-green-900' : 'text-gray-600 hover:text-green-900' }}">
                        Track Order
                    </a>
                    <a href="{{ route('about') }}"
                        class="transition py-1 {{ request()->routeIs('about') ? 'text-green-900 border-b-2 border-green-900' : 'text-gray-600 hover:text-green-900' }}">
                        Heritage & Unit
                    </a>
                    <a href="{{ route('contact') }}"
                        class="transition py-1 {{ request()->routeIs('contact') ? 'text-green-900 border-b-2 border-green-900' : 'text-gray-600 hover:text-green-900' }}">
                        Contact Us
                    </a>
                </nav>

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <button type="button" x-on:click="searchOpen = !searchOpen"
                        class="hidden lg:flex p-2.5 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition cursor-pointer"
                        aria-label="Search products">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>

                    <button type="button" x-on:click="cartOpen = true"
                        class="relative bg-green-950 hover:bg-green-900 text-white font-black text-xs px-4 sm:px-5 py-2.5 rounded-2xl transition shadow-xs flex items-center gap-2.5 cursor-pointer"
                        aria-label="Open quotation cart">
                        <span class="text-lg leading-none">🛍️</span>
                        <span class="hidden sm:inline">Quotation Cart</span>
                        <span class="bg-amber-400 text-gray-950 text-[10px] font-black px-2 py-0.5 rounded-full"
                            x-text="cartTotalCount">0</span>
                    </button>

                    <button type="button" x-on:click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden p-2.5 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition cursor-pointer"
                        aria-label="Toggle navigation menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

            </div>

            <!-- Desktop Search Field -->
            <div x-show="searchOpen" x-cloak class="hidden lg:block pb-4" x-transition>
                <div class="relative">
                    <input type="search" placeholder="Search for products..." x-model="searchQuery"
                        x-on:input.debounce.500ms="searchProducts"
                        x-on:keydown.escape="searchOpen = false; searchResults = []"
                        class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-800">
                    <div x-show="searchResults.length > 0"
                        class="absolute top-full mt-2 w-full bg-white shadow-2xl rounded-2xl border border-gray-100 max-h-96 overflow-y-auto z-50">
                        <template x-for="product in searchResults" :key="product.id">
                            <a :href="'/product/' + product.slug"
                                class="flex items-center gap-3 p-3 hover:bg-gray-50 transition border-b border-gray-50 last:border-0">
                                <img :src="product.image" :alt="product.name" class="w-12 h-12 object-cover rounded-lg"
                                    onerror="this.src='{{ asset('images/default-product.webp') }}'">
                                <div>
                                    <p class="text-xs font-bold text-gray-900" x-text="product.name"></p>
                                    <p class="text-[10px] text-gray-500" x-text="'₹' + product.price"></p>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Dropdown Menu -->
        <div x-show="mobileMenuOpen" x-cloak
            class="md:hidden bg-white border-b border-gray-200 px-5 py-4 space-y-3 shadow-lg">
            <div class="relative mb-3">
                <input type="search" placeholder="Search products..." x-model="searchQuery"
                    x-on:input.debounce.500ms="searchProducts"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-green-800">
                <div x-show="searchResults.length > 0"
                    class="absolute top-full mt-1 w-full bg-white shadow-2xl rounded-xl border border-gray-100 max-h-60 overflow-y-auto z-50">
                    <template x-for="product in searchResults" :key="product.id">
                        <a :href="'/product/' + product.slug" x-on:click="mobileMenuOpen = false; searchResults = []"
                            class="flex items-center gap-2 p-2.5 hover:bg-gray-50 transition border-b border-gray-50 last:border-0">
                            <img :src="product.image" :alt="product.name" class="w-10 h-10 object-cover rounded-lg"
                                onerror="this.src='{{ asset('images/default-product.webp') }}'">
                            <div>
                                <p class="text-xs font-bold text-gray-900" x-text="product.name"></p>
                                <p class="text-[10px] text-gray-500" x-text="'₹' + product.price"></p>
                            </div>
                        </a>
                    </template>
                </div>
            </div>

            <a href="{{ route('home') }}" x-on:click="mobileMenuOpen = false"
                class="flex items-center justify-between py-2.5 text-xs font-black uppercase text-gray-800 border-b border-gray-100">
                <span>🏡 Home</span><span>→</span>
            </a>
            <a href="{{ route('shop') }}" x-on:click="mobileMenuOpen = false"
                class="flex items-center justify-between py-2.5 text-xs font-black uppercase text-green-900 border-b border-gray-100">
                <span>🛒 Product Catalog</span><span>→</span>
            </a>
            <a href="{{ route('customer.account') }}" x-on:click="mobileMenuOpen = false"
                class="flex items-center justify-between py-2.5 text-xs font-black uppercase text-gray-800 border-b border-gray-100">
                <span>📦 Track Quotation / Order</span><span>→</span>
            </a>
            <a href="{{ route('about') }}" x-on:click="mobileMenuOpen = false"
                class="flex items-center justify-between py-2.5 text-xs font-black uppercase text-gray-800 border-b border-gray-100">
                <span>📜 Morjim Factory Heritage</span><span>→</span>
            </a>
            <a href="{{ route('contact') }}" x-on:click="mobileMenuOpen = false"
                class="flex items-center justify-between py-2.5 text-xs font-black uppercase text-gray-800">
                <span>📞 Contact Us</span><span>→</span>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1" id="main-content">
        @yield('content')
    </main>

    <!-- Mobile Bottom App Bar -->
    <div
        class="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 px-4 py-2 flex items-center justify-around shadow-2xl safe-area-inset-bottom">
        <a href="{{ route('home') }}"
            class="flex flex-col items-center gap-0.5 text-[10px] font-black {{ request()->routeIs('home') ? 'text-green-900' : 'text-gray-500' }}">
            <span class="text-lg">🏡</span>
            <span>Home</span>
        </a>
        <a href="{{ route('shop') }}"
            class="flex flex-col items-center gap-0.5 text-[10px] font-black {{ request()->routeIs('shop') || request()->routeIs('catalog.*') ? 'text-green-900' : 'text-gray-500' }}">
            <span class="text-lg">🛒</span>
            <span>Shop</span>
        </a>
        <a href="https://wa.me/{{ $adminPhone }}?text=Hello%20PatSons!%20I%20have%20an%20inquiry." target="_blank"
            rel="noopener noreferrer"
            class="flex flex-col items-center gap-0.5 text-[10px] font-black text-emerald-700">
            <span class="text-lg">💬</span>
            <span>WhatsApp</span>
        </a>
        <button type="button" x-on:click="cartOpen = true"
            class="flex flex-col items-center gap-0.5 text-[10px] font-black text-green-900 relative cursor-pointer">
            <span class="text-lg">🛍️</span>
            <span>Cart</span>
            <span
                class="absolute -top-1 right-1 bg-amber-400 text-gray-950 text-[9px] font-black px-1.5 py-0.2 rounded-full"
                x-text="cartTotalCount">0</span>
        </button>
    </div>

    <!-- Quotation Cart Slide-Over Drawer -->
    <div x-show="cartOpen" x-cloak class="fixed inset-0 z-50 overflow-hidden"
        x-on:keydown.escape.window="cartOpen = false">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-xs transition-opacity" x-on:click="cartOpen = false"
            x-show="cartOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-6 sm:pl-10">
            <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between" x-show="cartOpen"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">

                <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-green-950 text-white">
                    <div class="flex items-center gap-2">
                        <img src="{{ $logoUrl }}" alt="Logo" class="h-8 w-auto object-contain brightness-0 invert">
                        <h2 class="text-xs font-black uppercase tracking-wider">Quotation Cart</h2>
                    </div>
                    <button type="button" x-on:click="cartOpen = false"
                        class="text-white hover:text-amber-300 font-black text-xl p-1 cursor-pointer">&times;</button>
                </div>

                <div class="flex-1 overflow-y-auto p-5 space-y-4">
                    <template x-if="cartItems.length === 0">
                        <div class="py-12 text-center space-y-3">
                            <span class="text-4xl block">🍃</span>
                            <p class="text-xs font-bold text-gray-400">Your quotation cart is currently empty.</p>
                            <a href="{{ route('shop') }}" x-on:click="cartOpen = false"
                                class="inline-block bg-green-900 text-white text-xs font-black px-5 py-2.5 rounded-xl shadow-xs cursor-pointer">
                                Browse Products →
                            </a>
                        </div>
                    </template>

                    <template x-for="(item, idx) in cartItems" :key="idx">
                        <div
                            class="flex items-center justify-between p-3.5 bg-gray-50 border border-gray-200 rounded-2xl">
                            <div class="space-y-0.5">
                                <h4 class="text-xs font-black text-gray-900" x-text="item.name"></h4>
                                <span class="text-[10px] text-gray-500 font-bold" x-text="item.size"></span>
                                <div class="text-xs font-black text-green-900">
                                    ₹<span x-text="(item.price * item.quantity).toFixed(2)"></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" x-on:click="decrementQty(idx)"
                                    class="w-7 h-7 rounded-lg bg-white border border-gray-200 font-black text-xs cursor-pointer">-</button>
                                <span class="text-xs font-black w-5 text-center" x-text="item.quantity"></span>
                                <button type="button" x-on:click="incrementQty(idx)"
                                    class="w-7 h-7 rounded-lg bg-white border border-gray-200 font-black text-xs cursor-pointer">+</button>
                                <button type="button" x-on:click="removeItem(idx)"
                                    class="text-red-500 hover:text-red-700 font-black text-xs p-1 ml-1 cursor-pointer">&times;</button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="p-5 border-t border-gray-100 bg-gray-50 space-y-3" x-show="cartItems.length > 0">
                    <div class="flex items-baseline justify-between">
                        <span class="text-xs font-bold text-gray-500">Estimated Total:</span>
                        <span class="text-2xl font-black text-gray-900">₹<span
                                x-text="cartTotalAmount.toFixed(2)"></span></span>
                    </div>

                    <template x-if="step === 'phone'">
                        <div class="space-y-2.5">
                            <div>
                                <label class="block text-[10px] font-black text-gray-600 uppercase">Your WhatsApp Phone
                                    *</label>
                                <input type="tel" x-model="customerPhone" placeholder="10-digit WhatsApp number"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-xs font-bold outline-none focus:ring-2 focus:ring-green-800">
                            </div>
                            <button type="button" x-on:click="requestOtp()" :disabled="isSubmitting"
                                class="w-full bg-amber-400 hover:bg-amber-300 text-gray-950 font-black text-xs py-3 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                                <span x-show="!isSubmitting">📲 Send 4-Digit Verification Code</span>
                                <span x-show="isSubmitting" x-cloak>Sending OTP...</span>
                            </button>
                        </div>
                    </template>

                    <template x-if="step === 'otp'">
                        <div class="space-y-2.5">
                            <div class="bg-green-50 border border-green-200 p-2.5 rounded-xl text-center">
                                <span class="text-[11px] text-green-900 font-bold block">Code sent to +91 <span
                                        x-text="customerPhone"></span></span>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-600 uppercase">Enter 4-Digit Code
                                    *</label>
                                <input type="text" maxlength="4" x-model="otpCode" placeholder="• • • •"
                                    x-on:input="otpCode = otpCode.replace(/[^0-9]/g, '')"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-center text-lg font-black tracking-widest outline-none focus:ring-2 focus:ring-green-800">
                            </div>
                            <button type="button" x-on:click="verifyOtpCode()" :disabled="isSubmitting"
                                class="w-full bg-green-900 hover:bg-green-800 text-white font-black text-xs py-3 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                                <span x-show="!isSubmitting">✓ Verify Code & Proceed</span>
                                <span x-show="isSubmitting" x-cloak>Verifying...</span>
                            </button>
                            <button type="button" x-on:click="step = 'phone'; otpCode = ''"
                                class="text-[11px] text-gray-500 font-bold hover:underline block mx-auto cursor-pointer">
                                ← Change Phone Number
                            </button>
                        </div>
                    </template>

                    <template x-if="step === 'details'">
                        <div class="space-y-2.5">
                            <div class="bg-blue-50 border border-blue-200 p-2.5 rounded-xl text-center">
                                <span class="text-[11px] text-blue-900 font-bold">✓ Phone Verified: +91 <span
                                        x-text="customerPhone"></span></span>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-600 uppercase">Your Full Name
                                    *</label>
                                <input type="text" x-model="customerName" placeholder="e.g. Rahul Naik"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2 text-xs font-bold outline-none focus:ring-2 focus:ring-green-800">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-600 uppercase">Delivery Address /
                                    Notes *</label>
                                <textarea rows="2" x-model="deliveryAddress"
                                    placeholder="Enter house/shop address, landmark, town..."
                                    class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2 text-xs font-bold outline-none focus:ring-2 focus:ring-green-800"></textarea>
                            </div>
                            <button type="button" x-on:click="submitFinalOrder()" :disabled="isSubmitting"
                                class="w-full bg-amber-400 hover:bg-amber-300 text-gray-950 font-black text-xs py-3.5 px-4 rounded-2xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                                <span x-show="!isSubmitting">🚀 Place Final Quotation Order →</span>
                                <span x-show="isSubmitting" x-cloak>Placing Order...</span>
                            </button>
                        </div>
                    </template>
                </div>

            </div>
        </div>
    </div>

    <!-- Order Placed Success Modal -->
    <div x-show="orderSuccessModal" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl text-center space-y-5 border border-gray-100"
            x-on:click.away="orderSuccessModal = false">

            <div
                class="w-16 h-16 bg-emerald-100 text-emerald-800 rounded-full flex items-center justify-center mx-auto text-3xl animate-bounce">
                ✓
            </div>

            <div class="space-y-1.5">
                <h3 class="text-xl font-black text-gray-900">Order Placed Successfully!</h3>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Order Reference: <strong class="text-green-950 font-mono text-sm"
                        x-text="'#' + placedOrderNumber"></strong>
                </p>
            </div>

            <div class="bg-emerald-50 rounded-2xl p-4 border border-emerald-100 text-left space-y-2">
                <div class="flex items-center gap-2 text-emerald-900 text-xs font-black">
                    <span>💬</span>
                    <span>WhatsApp Notification Sent</span>
                </div>
                <p class="text-[11px] text-emerald-800 leading-relaxed font-medium">
                    A confirmation breakdown has been sent to your WhatsApp number. Our team at Dev Can Fruit Products
                    (Morjim Factory) will contact you shortly to confirm delivery.
                </p>
            </div>

            <div class="flex flex-col gap-2 pt-2">
                <a :href="placedRedirectUrl"
                    class="w-full bg-green-900 hover:bg-green-800 text-white font-black text-xs py-3 px-4 rounded-xl transition shadow-xs flex items-center justify-center gap-2">
                    <span>🔍 Track Order Status</span>
                </a>
                <button type="button" x-on:click="orderSuccessModal = false; cartOpen = false"
                    class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs py-3 px-4 rounded-xl transition cursor-pointer">
                    Continue Shopping
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-green-950 text-white text-xs border-t border-green-900 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-1 sm:grid-cols-3 gap-8 items-start">
            <div class="space-y-3">
                <a href="{{ route('home') }}" class="inline-block bg-white p-2.5 rounded-2xl shadow-md">
                    <img src="{{ $logoUrl }}" alt="PatSons Goa Official Logo" class="h-12 w-auto object-contain">
                </a>
                <p class="text-green-200/70 text-[11px] leading-relaxed">
                    Manufactured by Dev Can Fruit Products, Morjim, Pernem - Goa.<br>
                    FSSAI Lic. No. <strong>10613001000730</strong>
                </p>
            </div>

            <div class="space-y-1 font-bold text-green-100">
                <h4 class="text-amber-400 uppercase text-[10px] tracking-widest font-black mb-2">Quick Navigation</h4>
                <a href="{{ route('shop') }}" class="block hover:text-white transition">Product Catalog</a>
                <a href="{{ route('customer.account') }}" class="block hover:text-white transition">Track Order /
                    Reorder</a>
                <a href="{{ route('about') }}" class="block hover:text-white transition">Heritage & Quality</a>
            </div>

            <div class="space-y-1 text-green-200/80">
                <h4 class="text-amber-400 uppercase text-[10px] tracking-widest font-black mb-2">Support & Inquiries
                </h4>
                <p>Phone: +{{ $adminPhone }}</p>
                <p>Location: Morjim, Goa 403512</p>
                <a href="{{ route('admin.dashboard') }}" class="text-green-400 hover:underline block pt-2">Admin Portal
                    ↗</a>
            </div>
        </div>
        <div class="border-t border-green-900 py-4 text-center text-[10px] text-green-400">
            <p>&copy; {{ date('Y') }} Dev Can Fruit Products (PatSons Foods Goa). All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Global Cart Script -->
    <script>
        function cartManager() {
            return {
                cartOpen: false,
                orderSuccessModal: false,
                placedOrderNumber: '',
                placedRedirectUrl: '{{ route('customer.account') }}',
                isSubmitting: false,
                step: 'phone',
                customerPhone: '{{ session('customer_phone', '') }}',
                otpCode: '',
                customerName: '',
                deliveryAddress: '',
                cartItems: JSON.parse(localStorage.getItem('patsons_cart') || '[]'),

                get cartTotalCount() {
                    return this.cartItems.reduce((sum, item) => sum + (item.quantity || 1), 0);
                },
                get cartTotalAmount() {
                    return this.cartItems.reduce((sum, item) => sum + ((item.price || 0) * (item.quantity || 1)), 0);
                },
                saveCart() {
                    localStorage.setItem('patsons_cart', JSON.stringify(this.cartItems));
                },
                addToCart(id, name, price, size, image) {
                    let existing = this.cartItems.find(i => i.id === id);
                    if (existing) {
                        existing.quantity = (existing.quantity || 1) + 1;
                    } else {
                        this.cartItems.push({ id, name, price, size, image, quantity: 1 });
                    }
                    this.saveCart();
                    this.cartOpen = true;
                },
                incrementQty(idx) {
                    this.cartItems[idx].quantity++;
                    this.saveCart();
                },
                decrementQty(idx) {
                    if (this.cartItems[idx].quantity > 1) {
                        this.cartItems[idx].quantity--;
                    } else {
                        this.removeItem(idx);
                    }
                    this.saveCart();
                },
                removeItem(idx) {
                    this.cartItems.splice(idx, 1);
                    this.saveCart();
                },
                async requestOtp() {
                    if (!this.customerPhone || this.customerPhone.trim().length < 10) {
                        alert('Please enter a valid 10-digit WhatsApp number.');
                        return;
                    }
                    this.isSubmitting = true;
                    try {
                        let res = await fetch('{{ route('order.sendOtp') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ phone: this.customerPhone })
                        });
                        let data = await res.json();
                        if (data.status === 'success') {
                            if (data.existing_name) this.customerName = data.existing_name;
                            if (data.existing_address) this.deliveryAddress = data.existing_address;
                            this.step = 'otp';
                        } else {
                            alert(data.message || 'Failed to send OTP.');
                        }
                    } catch (e) {
                        alert('Error sending OTP: ' + e.message);
                    } finally {
                        this.isSubmitting = false;
                    }
                },
                async verifyOtpCode() {
                    if (!this.otpCode || this.otpCode.trim().length !== 4) {
                        alert('Please enter the 4-digit code sent to your WhatsApp.');
                        return;
                    }
                    this.isSubmitting = true;
                    try {
                        let res = await fetch('{{ route('order.verifyOtp') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ phone: this.customerPhone, otp: this.otpCode })
                        });
                        let data = await res.json();
                        if (data.status === 'success') {
                            this.step = 'details';
                        } else {
                            alert(data.message || 'Invalid OTP code.');
                        }
                    } catch (e) {
                        alert('Verification failed: ' + e.message);
                    } finally {
                        this.isSubmitting = false;
                    }
                },
                async submitFinalOrder() {
                    if (!this.customerName.trim()) {
                        alert('Please enter your name.');
                        return;
                    }
                    if (!this.deliveryAddress.trim()) {
                        alert('Please provide your delivery address or location.');
                        return;
                    }
                    this.isSubmitting = true;
                    try {
                        let res = await fetch('{{ route('order.store') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({
                                customer_phone: this.customerPhone,
                                customer_name: this.customerName,
                                delivery_address: this.deliveryAddress,
                                cart_items: this.cartItems
                            })
                        });
                        let data = await res.json();
                        if (data.status === 'success') {
                            this.cartItems = [];
                            this.saveCart();
                            this.cartOpen = false;
                            this.step = 'phone';
                            this.otpCode = '';
                            this.placedOrderNumber = data.order_number || '';
                            if (data.redirect_url) this.placedRedirectUrl = data.redirect_url;
                            this.orderSuccessModal = true;
                        } else {
                            alert(data.message || 'Failed to place order.');
                        }
                    } catch (e) {
                        alert('Error submitting order: ' + e.message);
                    } finally {
                        this.isSubmitting = false;
                    }
                },
                async searchProducts() {
                    if (!this.searchQuery || this.searchQuery.trim().length < 2) {
                        this.searchResults = [];
                        return;
                    }
                    try {
                        let res = await fetch(`{{ $searchRoute }}?q=${encodeURIComponent(this.searchQuery)}`);
                        let data = await res.json();
                        this.searchResults = data.products || [];
                    } catch (e) {
                        this.searchResults = [];
                    }
                }
            };
        }
    </script>

    @stack('scripts')
</body>

</html>