<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
    $pageTitle = html_entity_decode(trim($__env->yieldContent('meta_title')) ?: (trim($__env->yieldContent('title')) ?: 'Wholesale Goan Kokum, Jams & Syrups | PatSons Goa'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $pageDescription = html_entity_decode(trim($__env->yieldContent('meta_description')) ?: 'Authentic Goan Kokum Agal, handcrafted Cashew Fruit Jams, herbal digestifs, cocktail cordials, and refreshing squashes direct from Dev Can Fruit Products, Morjim, Pernem - Goa.', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $pageKeywords = html_entity_decode(trim($__env->yieldContent('meta_keywords')) ?: 'PatSons Foods Goa, Dev Can Fruit Products, Goan Kokum Agal, Cashew Jam Goa, Kokum Syrup, Pachak Appetizer, Bar Syrups Goa, Fruit Crushes Goa, Morjim Factory', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $adminPhone = \App\Models\Setting::get('whatsapp_admin_number', config('services.whatsapp.admin_number', '917758943614'));

    // Check candidate logo paths across both standard Laravel public path and cPanel public_html
    $logoCandidates = [
        'images/logo1.webp',
        'images/products/logo1.webp',
        'images/logo.png',
        'images/logo.webp',
    ];

    $resolvedLogoPath = 'images/logo1.webp';
    foreach ($logoCandidates as $candidate) {
        if (
            file_exists(public_path($candidate)) ||
            file_exists(base_path('../../public_html/' . $candidate)) ||
            (isset($_SERVER['DOCUMENT_ROOT']) && file_exists(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . ltrim($candidate, '/')))
        ) {
            $resolvedLogoPath = $candidate;
            break;
        }
    }
    $logoUrl = asset($resolvedLogoPath);

    $ogImage = trim($__env->yieldContent('og_image')) ?: $logoUrl;
    $searchRoute = \Illuminate\Support\Facades\Route::has('api.products.search') ? route('api.products.search') : url('/shop');

    $globalSchema = [
        "@context" => "https://schema.org",
        "@graph" => [
            [
                "@type" => ["LocalBusiness", "FoodEstablishment"],
                "@id" => url('/') . "/#organization",
                "name" => "PatSons Foods (Dev Can Fruit Products)",
                "legalName" => "Dev Can Fruit Products",
                "alternateName" => ["PatSons Foods", "PatSons Goa", "Dev Can Fruit Products Morjim"],
                "url" => url('/'),
                "logo" => [
                    "@type" => "ImageObject",
                    "@id" => url('/') . "/#logo",
                    "url" => $logoUrl,
                    "caption" => "PatSons Foods Official Logo"
                ],
                "image" => $logoUrl,
                "description" => "Authentic Goan food manufacturer since 1990. Handcrafted Kokum Agal (pure Sol Kadhi extract), Cashew Fruit Jams, Pachak Appetizer, fruit squashes, and bar syrups manufactured in Morjim, Pernem - Goa.",
                "telephone" => "+" . $adminPhone,
                "email" => "patsons1993@rediffmail.com",
                "foundingDate" => "1990",
                "founder" => [
                    "@type" => "Person",
                    "name" => "Vivek Laxman Patki"
                ],
                "priceRange" => "₹₹",
                "currenciesAccepted" => "INR",
                "paymentAccepted" => "Cash, UPI, Bank Transfer",
                "hasMap" => "https://maps.google.com/?q=Dev+Can+Fruit+Products+Morjim+Pernem+Goa",
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
                "openingHoursSpecification" => [
                    [
                        "@type" => "OpeningHoursSpecification",
                        "dayOfWeek" => ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
                        "opens" => "09:00",
                        "closes" => "18:30"
                    ]
                ],
                "areaServed" => [
                    [
                        "@type" => "AdministrativeArea",
                        "name" => "Goa"
                    ],
                    [
                        "@type" => "Country",
                        "name" => "India"
                    ]
                ],
                "knowsAbout" => [
                    "Goan Kokum Agal",
                    "Sol Kadhi Extract",
                    "Cashew Fruit Jams",
                    "Cashew Mango Jam",
                    "Kokum Squash",
                    "Pachak Appetizer Crush",
                    "Bar Syrups Goa",
                    "Blue Curacao Syrup",
                    "Wholesale Food Supply Goa",
                    "HoReCa Food Service",
                    "Dev Can Fruit Products Morjim"
                ],
                "contactPoint" => [
                    [
                        "@type" => "ContactPoint",
                        "telephone" => "+" . $adminPhone,
                        "contactType" => "Customer Service & Trade Inquiries",
                        "areaServed" => "IN",
                        "availableLanguage" => ["English", "Hindi", "Konkani", "Marathi"]
                    ]
                ]
            ],
            [
                "@type" => "WebSite",
                "@id" => url('/') . "/#website",
                "url" => url('/'),
                "name" => "PatSons Foods Goa",
                "alternateName" => "PatSons Wholesale Catalog",
                "publisher" => [
                    "@id" => url('/') . "/#organization"
                ],
                "inLanguage" => ["en", "gom", "mr", "hi"],
                "potentialAction" => [
                    "@type" => "SearchAction",
                    "target" => (\Illuminate\Support\Facades\Route::has('shop') ? route('shop') : url('/shop')) . "?search={search_term_string}",
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
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Hreflang Multi-Language Annotations -->
    <link rel="alternate" hreflang="en" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="gom" href="{{ url()->current() }}?lang=gom">
    <link rel="alternate" hreflang="mr" href="{{ url()->current() }}?lang=mr">
    <link rel="alternate" hreflang="hi" href="{{ url()->current() }}?lang=hi">
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

    <!-- Geo & Local SEO Meta -->
    <meta name="geo.region" content="IN-GA">
    <meta name="geo.placename" content="Morjim, Pernem, Goa">
    <meta name="geo.position" content="15.6322;73.7438">
    <meta name="ICBM" content="15.6322, 73.7438">
    <meta name="theme-color" content="#052e16">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="PatSons Goa">
    <meta name="application-name" content="PatSons Foods">
    <meta name="msapplication-TileColor" content="#052e16">
    <meta name="format-detection" content="telephone=no">

    <!-- Open Graph / Facebook Meta -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="PatSons Foods Goa">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:secure_url" content="{{ $ogImage }}">
    <meta property="og:image:alt" content="{{ $pageTitle }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="en_IN">
    <meta property="og:locale:alternate" content="mr_IN">
    <meta property="og:locale:alternate" content="hi_IN">

    <!-- Twitter Card Meta -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="@yield('canonical', url()->current())">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <meta name="twitter:image:alt" content="{{ $pageTitle }}">

    <!-- Favicon & Web Manifest -->
    <link rel="icon" type="image/webp" href="{{ $logoUrl }}">
    <link rel="apple-touch-icon" href="{{ $logoUrl }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- DNS Prefetch & Preconnect for Peak Core Web Vitals -->
    <link rel="dns-prefetch" href="//cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="//unpkg.com">
    <link rel="dns-prefetch" href="//www.googletagmanager.com">
    <link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com" crossorigin>
    <link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>

    @stack('preload')

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

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-FBCQW44FK2"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-FBCQW44FK2');
    </script>

    @stack('schema')
</head>

<body class="bg-stone-50 text-gray-900 min-h-screen flex flex-col font-sans antialiased pb-20 sm:pb-0"
    x-data="cartManager()"
    x-on:add-to-cart.window="addToCart($event.detail.id, $event.detail.name, $event.detail.price, $event.detail.size, $event.detail.image)">

    <!-- Top Utility Bar with Multi-Language Switcher -->
    <div class="bg-green-950 text-white text-[11px] py-2 px-4 border-b border-green-900">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span class="font-bold text-amber-200">🌴 Dev Can Fruit Products • Morjim, Pernem - Goa</span>
            </div>

            <div class="flex items-center gap-3 font-bold text-green-200">
                <!-- Multi-Language Switcher (EN, Konkani, Marathi, Hindi) -->
                <div class="flex items-center gap-1.5 text-[10px] bg-green-900/80 px-2.5 py-1 rounded-full border border-green-800">
                    <span class="text-amber-300">🌐</span>
                    <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'text-amber-300 font-black underline' : 'text-green-200 hover:text-white' }}" title="English">EN</a>
                    <span class="text-green-700">|</span>
                    <a href="{{ route('lang.switch', 'gom') }}" class="{{ app()->getLocale() === 'gom' ? 'text-amber-300 font-black underline' : 'text-green-200 hover:text-white' }}" title="Konkani">कोंकणी</a>
                    <span class="text-green-700">|</span>
                    <a href="{{ route('lang.switch', 'mr') }}" class="{{ app()->getLocale() === 'mr' ? 'text-amber-300 font-black underline' : 'text-green-200 hover:text-white' }}" title="Marathi">मराठी</a>
                    <span class="text-green-700">|</span>
                    <a href="{{ route('lang.switch', 'hi') }}" class="{{ app()->getLocale() === 'hi' ? 'text-amber-300 font-black underline' : 'text-green-200 hover:text-white' }}" title="Hindi">हिंदी</a>
                </div>

                <a href="https://wa.me/{{ $adminPhone }}" target="_blank" rel="noopener noreferrer"
                    class="hidden sm:flex hover:text-white transition items-center gap-1">
                    <span>💬 {{ __('Inquiries') }}:</span>
                    <span class="text-white font-mono">+{{ $adminPhone }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navbar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-xs"
        x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative flex items-center justify-between h-20 sm:h-24">

                <!-- Brand Logo (Left-aligned) -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('home') }}"
                        class="relative flex items-center h-18 sm:h-20 w-48 sm:w-72 group z-30"
                        aria-label="PatSons Foods Goa Home">
                        <img src="{{ $logoUrl }}" alt="PatSons Goa Official Logo"
                            class="absolute left-0 top-1/2 -translate-y-1/2 h-28 sm:h-36 w-auto max-w-none object-contain transition-transform duration-300 group-hover:scale-105 drop-shadow-lg pointer-events-auto">
                    </a>
                </div>

                <!-- Desktop Navigation Links (Centered) -->
                <nav
                    class="hidden md:flex absolute left-1/2 -translate-x-1/2 items-center gap-7 text-xs font-black uppercase tracking-wider">
                    <a href="{{ route('home') }}"
                        class="transition py-1 {{ request()->routeIs('home') ? 'text-green-900 border-b-2 border-green-900' : 'text-gray-600 hover:text-green-900' }}">
                        {{ __('Home') }}
                    </a>
                    <a href="{{ route('shop') }}"
                        class="transition py-1 {{ request()->routeIs('shop') || request()->routeIs('catalog.*') ? 'text-green-900 border-b-2 border-green-900' : 'text-gray-600 hover:text-green-900' }}">
                        {{ __('Catalog') }}
                    </a>
                    <a href="{{ route('about') }}"
                        class="transition py-1 {{ request()->routeIs('about') ? 'text-green-900 border-b-2 border-green-900' : 'text-gray-600 hover:text-green-900' }}">
                        {{ __('Heritage & Unit') }}
                    </a>
                    <a href="{{ route('contact') }}"
                        class="transition py-1 {{ request()->routeIs('contact') ? 'text-green-900 border-b-2 border-green-900' : 'text-gray-600 hover:text-green-900' }}">
                        {{ __('Contact Us') }}
                    </a>
                </nav>

                <!-- Actions: Desktop Navigation Contact & Mobile Toggle -->
                <div class="flex items-center gap-3">
                    {{-- Commented out Quotation Cart button for view-only catalog display
                    <button type="button" x-on:click="cartOpen = true"
                        class="hidden sm:inline-flex items-center gap-2 bg-green-900 hover:bg-green-800 text-white text-xs font-black px-4 py-2.5 rounded-2xl shadow-xs transition active:scale-95 cursor-pointer">
                        <span>🛍️</span>
                        <span>{{ __('Quotation Cart') }}</span>
                        <span class="bg-amber-400 text-gray-950 text-[10px] font-black px-1.5 py-0.2 rounded-full"
                            x-text="cartTotalCount">0</span>
                    </button>
                    --}}
                    <a href="{{ route('contact') }}"
                        class="hidden sm:inline-flex items-center gap-1.5 bg-green-900 hover:bg-green-800 text-white text-xs font-black px-4 py-2.5 rounded-2xl shadow-xs transition active:scale-95">
                        <span>📞</span>
                        <span>{{ __('Inquire Now') }}</span>
                    </a>

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
        </div>

        <!-- Mobile Dropdown Menu -->
        <div x-show="mobileMenuOpen" x-cloak
            class="md:hidden bg-white border-b border-gray-200 px-5 py-4 space-y-3 shadow-lg">
            <a href="{{ route('home') }}" x-on:click="mobileMenuOpen = false"
                class="flex items-center justify-between py-2.5 text-xs font-black uppercase text-gray-800 border-b border-gray-100">
                <span>🏡 Home</span><span>→</span>
            </a>
            <a href="{{ route('shop') }}" x-on:click="mobileMenuOpen = false"
                class="flex items-center justify-between py-2.5 text-xs font-black uppercase text-green-900 border-b border-gray-100">
                <span>🛒 Product Catalog</span><span>→</span>
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

        <a href="{{ route('about') }}"
            class="flex flex-col items-center gap-0.5 text-[10px] font-black {{ request()->routeIs('about') ? 'text-green-900' : 'text-gray-500' }}">
            <span class="text-lg">🏭</span>
            <span>Factory</span>
        </a>

        {{-- Commented out Cart button for view-only catalog display
        <button type="button" x-on:click="cartOpen = true"
            class="flex flex-col items-center gap-0.5 text-[10px] font-black text-green-900 relative cursor-pointer">
            <span class="text-lg">🛍️</span>
            <span>{{ __('Cart') }}</span>
            <span
                class="absolute -top-1 right-1 bg-amber-400 text-gray-950 text-[9px] font-black px-1.5 py-0.2 rounded-full"
                x-text="cartTotalCount">0</span>
        </button>
        --}}
    </div>

    {{-- QUOTATION CART SLIDE-OVER & ORDER SUCCESS MODAL COMMENTED OUT FOR VIEW-ONLY CATALOG DISPLAY
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

                <!-- Drawer Header -->
                <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between bg-green-950 text-white">
                    <div class="flex items-center gap-2.5">
                        <img src="{{ $logoUrl }}" alt="Logo" class="h-8 w-auto object-contain brightness-0 invert">
                        <div>
                            <h2 class="text-xs font-black uppercase tracking-wider">{{ __('Quotation Cart') }}</h2>
                            <span class="text-[10px] text-green-300 font-bold" x-text="cartTotalCount + ' item(s) selected'"></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" x-show="cartItems.length > 0" x-on:click="clearCart()"
                            class="text-[10px] text-red-300 hover:text-red-100 font-bold underline px-2 py-1 cursor-pointer">
                            {{ __('Clear') }}
                        </button>
                        <button type="button" x-on:click="cartOpen = false"
                            class="text-white hover:text-amber-300 font-black text-2xl p-1 leading-none cursor-pointer">&times;</button>
                    </div>
                </div>

                <!-- Drawer Items List -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3">
                    <template x-if="cartItems.length === 0">
                        <div class="py-16 text-center space-y-3">
                            <span class="text-5xl block animate-bounce">🍃</span>
                            <h3 class="text-sm font-black text-gray-800">{{ __('Your quotation cart is empty') }}</h3>
                            <p class="text-xs font-bold text-gray-400 max-w-xs mx-auto">Add wholesale boxes of authentic Goan Kokum Agal, Cashew Jam, or Syrups.</p>
                            <a href="{{ route('shop') }}" x-on:click="cartOpen = false"
                                class="inline-block bg-green-900 hover:bg-green-800 text-white text-xs font-black px-6 py-3 rounded-xl shadow-xs cursor-pointer transition">
                                {{ __('Browse Products') }} →
                            </a>
                        </div>
                    </template>

                    <template x-for="(item, idx) in cartItems" :key="idx">
                        <div class="flex items-center justify-between p-3.5 bg-gray-50 border border-gray-200 rounded-2xl gap-3">
                            <div class="space-y-0.5 flex-1 min-w-0">
                                <h4 class="text-xs font-black text-gray-900 truncate" x-text="item.name"></h4>
                                <div class="flex items-center gap-2 text-[10px] text-gray-500 font-bold">
                                    <span class="bg-gray-200 text-gray-700 px-1.5 py-0.5 rounded" x-text="item.size || 'Pack'"></span>
                                    <span>₹<span x-text="Number(item.price || 0).toFixed(2)"></span> / unit</span>
                                </div>
                                <div class="text-xs font-black text-green-900">
                                    Total: ₹<span x-text="(Number(item.price || 0) * (item.quantity || 1)).toFixed(2)"></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" x-on:click="decrementQty(idx)"
                                    class="w-7 h-7 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 font-black text-xs cursor-pointer flex items-center justify-center">-</button>
                                <span class="text-xs font-black w-6 text-center" x-text="item.quantity"></span>
                                <button type="button" x-on:click="incrementQty(idx)"
                                    class="w-7 h-7 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 font-black text-xs cursor-pointer flex items-center justify-center">+</button>
                                <button type="button" x-on:click="removeItem(idx)"
                                    class="text-red-500 hover:text-red-700 font-black text-base p-1 ml-1 cursor-pointer" title="Remove">&times;</button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Drawer Footer Actions -->
                <div class="p-4 sm:p-5 border-t border-gray-100 bg-gray-50 space-y-3" x-show="cartItems.length > 0">
                    <div class="flex items-baseline justify-between bg-white p-3 rounded-xl border border-gray-200">
                        <div>
                            <span class="text-[10px] uppercase tracking-wider font-black text-gray-500 block">{{ __('Estimated Total') }}</span>
                            <span class="text-[10px] text-gray-400 font-bold">Ex-factory Morjim • GST extra</span>
                        </div>
                        <span class="text-2xl font-black text-green-950">₹<span x-text="cartTotalAmount.toFixed(2)"></span></span>
                    </div>

                    <!-- 1-Click WhatsApp Direct Quote Button -->
                    <button type="button" x-on:click="openWhatsAppQuotation()"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs py-3.5 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                        <span class="text-base">💬</span>
                        <span>{{ __('Instant 1-Click WhatsApp Quote') }}</span>
                    </button>

                    <!-- Divider -->
                    <div class="relative flex py-1 items-center">
                        <div class="flex-grow border-t border-gray-200"></div>
                        <span class="flex-shrink mx-2 text-[10px] font-black uppercase text-gray-400 tracking-wider">or place tracked order</span>
                        <div class="flex-grow border-t border-gray-200"></div>
                    </div>

                    <!-- Step 1: Customer Phone -->
                    <template x-if="step === 'phone'">
                        <div class="space-y-2">
                            <div>
                                <label class="block text-[10px] font-black text-gray-600 uppercase mb-1">Your WhatsApp Number *</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-gray-300 bg-gray-100 text-gray-700 text-xs font-black">+91</span>
                                    <input type="tel" x-model="customerPhone" maxlength="10" placeholder="10-digit number"
                                        x-on:input="customerPhone = customerPhone.replace(/[^0-9]/g, '')"
                                        class="w-full bg-white border border-gray-300 rounded-r-xl px-3 py-2 text-xs font-bold outline-none focus:ring-2 focus:ring-green-800">
                                </div>
                            </div>
                            <button type="button" x-on:click="requestOtp()" :disabled="isSubmitting"
                                class="w-full bg-green-900 hover:bg-green-800 text-white font-black text-xs py-2.5 px-4 rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                                <span x-show="!isSubmitting">📲 Send Verification Code</span>
                                <span x-show="isSubmitting" x-cloak>Sending Code...</span>
                            </button>
                        </div>
                    </template>

                    <!-- Step 2: OTP Verification -->
                    <template x-if="step === 'otp'">
                        <div class="space-y-2">
                            <div class="bg-green-50 border border-green-200 p-2 rounded-xl text-center">
                                <span class="text-[11px] text-green-900 font-bold block">Code sent to +91 <span x-text="customerPhone"></span></span>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-600 uppercase mb-1">Enter 4-Digit Code *</label>
                                <input type="text" maxlength="4" x-model="otpCode" placeholder="• • • •"
                                    x-on:input="otpCode = otpCode.replace(/[^0-9]/g, '')"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-3 py-2 text-center text-lg font-black tracking-widest outline-none focus:ring-2 focus:ring-green-800">
                            </div>
                            <button type="button" x-on:click="verifyOtpCode()" :disabled="isSubmitting"
                                class="w-full bg-green-900 hover:bg-green-800 text-white font-black text-xs py-2.5 px-4 rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                                <span x-show="!isSubmitting">✓ Verify & Continue</span>
                                <span x-show="isSubmitting" x-cloak>Verifying...</span>
                            </button>
                            <button type="button" x-on:click="step = 'phone'; otpCode = ''"
                                class="text-[10px] text-gray-500 font-bold hover:underline block mx-auto cursor-pointer">
                                ← Change Phone Number
                            </button>
                        </div>
                    </template>

                    <!-- Step 3: Name & Delivery Address -->
                    <template x-if="step === 'details'">
                        <div class="space-y-2">
                            <div class="bg-blue-50 border border-blue-200 p-2 rounded-xl text-center">
                                <span class="text-[11px] text-blue-900 font-bold">✓ Verified: +91 <span x-text="customerPhone"></span></span>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-600 uppercase mb-1">Full Name *</label>
                                <input type="text" x-model="customerName" placeholder="e.g. Ramesh Naik"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-3 py-1.5 text-xs font-bold outline-none focus:ring-2 focus:ring-green-800">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-600 uppercase mb-1">Delivery Address / Notes *</label>
                                <textarea rows="2" x-model="deliveryAddress"
                                    placeholder="Shop/Hotel name, village/city, landmark..."
                                    class="w-full bg-white border border-gray-300 rounded-xl px-3 py-1.5 text-xs font-bold outline-none focus:ring-2 focus:ring-green-800"></textarea>
                            </div>
                            <button type="button" x-on:click="submitFinalOrder()" :disabled="isSubmitting"
                                class="w-full bg-amber-400 hover:bg-amber-300 text-gray-950 font-black text-xs py-3 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                                <span x-show="!isSubmitting">🚀 Place Factory Order →</span>
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
    --}}

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
                {{-- Commented out customer order tracking portal for view-only catalog display
                <a href="{{ route('customer.account') }}" class="block hover:text-white transition">Track Order / Reorder</a>
                --}}
                <a href="{{ route('about') }}" class="block hover:text-white transition">Heritage & Quality</a>
                <a href="{{ route('contact') }}" class="block hover:text-white transition">Contact Factory</a>
            </div>

            <div class="space-y-1 text-green-200/80">
                <h4 class="text-amber-400 uppercase text-[10px] tracking-widest font-black mb-2">Support & Inquiries
                </h4>
                <p>Phone: +{{ $adminPhone }}</p>
                <p>Location: Morjim, Goa 403512</p>
                <a href="{{ route('login') }}" class="text-green-400 hover:underline block pt-2">Admin Portal ↗</a>
            </div>
        </div>
        <div class="border-t border-green-900 py-4 text-center text-[10px] text-green-400">
            <p>&copy; {{ date('Y') }} Dev Can Fruit Products (PatSons Foods Goa). All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Global Cart & State Script -->
    <script>
        function cartManager() {
            return {
                cartOpen: false,
                cartItems: JSON.parse(localStorage.getItem('patsons_cart') || '[]'),
                step: 'phone',
                customerPhone: '',
                otpCode: '',
                customerName: '',
                deliveryAddress: '',
                isSubmitting: false,
                orderSuccessModal: false,
                placedOrderNumber: '',
                placedRedirectUrl: '{{ route('customer.account') }}',

                saveCart() {
                    localStorage.setItem('patsons_cart', JSON.stringify(this.cartItems));
                },
                get cartTotalCount() {
                    return this.cartItems.reduce((sum, item) => sum + (item.quantity || 1), 0);
                },
                get cartTotalAmount() {
                    return this.cartItems.reduce((sum, item) => sum + ((item.price || 0) * (item.quantity || 1)), 0);
                },
                addToCart(id, name, price, size, image) {
                    let existing = this.cartItems.find(i => i.id === id && i.size === size);
                    if (existing) {
                        existing.quantity = (existing.quantity || 1) + 1;
                    } else {
                        this.cartItems.push({ id, name, price, size, image, quantity: 1 });
                    }
                    this.saveCart();
                    this.cartOpen = true;
                },
                incrementQty(idx) {
                    if (this.cartItems[idx]) {
                        this.cartItems[idx].quantity = (this.cartItems[idx].quantity || 1) + 1;
                        this.saveCart();
                    }
                },
                decrementQty(idx) {
                    if (this.cartItems[idx]) {
                        if (this.cartItems[idx].quantity > 1) {
                            this.cartItems[idx].quantity -= 1;
                        } else {
                            this.cartItems.splice(idx, 1);
                        }
                        this.saveCart();
                    }
                },
                removeItem(idx) {
                    this.cartItems.splice(idx, 1);
                    this.saveCart();
                },
                clearCart() {
                    if (confirm('Are you sure you want to clear your cart?')) {
                        this.cartItems = [];
                        this.saveCart();
                    }
                },
                openWhatsAppQuotation() {
                    if (this.cartItems.length === 0) {
                        alert('Your quotation cart is currently empty.');
                        return;
                    }
                    let lines = [
                        "🌴 *QUOTATION INQUIRY - PATSONS FOODS GOA*",
                        "Dev Can Fruit Products, Morjim, Goa",
                        "━━━━━━━━━━━━━━━━━━━━"
                    ];
                    this.cartItems.forEach(item => {
                        let qty = item.quantity || 1;
                        let pr = Number(item.price || 0);
                        let sub = qty * pr;
                        lines.push(`• *${item.name}* (${item.size || 'Pack'})`);
                        lines.push(`  Qty: ${qty} | Unit: ₹${pr.toFixed(2)} | Subtotal: ₹${sub.toFixed(2)}`);
                    });
                    lines.push("━━━━━━━━━━━━━━━━━━━━");
                    lines.push(`💰 *Total Est. Amount:* ₹${this.cartTotalAmount.toFixed(2)}`);
                    lines.push("");
                    lines.push("Please provide the wholesale/trade quote and delivery availability.");

                    let encoded = encodeURIComponent(lines.join("\n"));
                    window.open(`https://wa.me/{{ $adminPhone }}?text=${encoded}`, '_blank');
                },
                async requestOtp() {
                    let clean = (this.customerPhone || '').replace(/[^0-9]/g, '');
                    if (clean.length < 10) {
                        alert('Please enter a valid 10-digit WhatsApp number.');
                        return;
                    }
                    this.customerPhone = clean.slice(-10);
                    this.isSubmitting = true;
                    try {
                        let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        let res = await fetch('{{ route('order.sendOtp') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({ phone: this.customerPhone })
                        });
                        let data = await res.json();
                        if (res.ok && data.status === 'success') {
                            if (data.existing_name) this.customerName = data.existing_name;
                            if (data.existing_address) this.deliveryAddress = data.existing_address;
                            this.step = 'otp';
                        } else {
                            alert(data.message || 'Failed to send OTP code. Please try again.');
                        }
                    } catch (e) {
                        alert('Network error while requesting verification code.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },
                async verifyOtpCode() {
                    if (!this.otpCode || this.otpCode.length !== 4) {
                        alert('Please enter the 4-digit verification code.');
                        return;
                    }
                    this.isSubmitting = true;
                    try {
                        let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        let res = await fetch('{{ route('order.verifyOtp') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({ phone: this.customerPhone, otp: this.otpCode })
                        });
                        let data = await res.json();
                        if (res.ok && data.status === 'success') {
                            this.step = 'details';
                        } else {
                            alert(data.message || 'Invalid or expired OTP code.');
                        }
                    } catch (e) {
                        alert('Verification error. Please try again.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },
                async submitFinalOrder() {
                    if (!this.customerName.trim()) {
                        alert('Please provide your name.');
                        return;
                    }
                    if (!this.deliveryAddress.trim()) {
                        alert('Please provide your delivery address or town in Goa.');
                        return;
                    }
                    this.isSubmitting = true;
                    try {
                        let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        let res = await fetch('{{ route('order.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                customer_phone: this.customerPhone,
                                customer_name: this.customerName,
                                delivery_address: this.deliveryAddress,
                                cart_items: this.cartItems
                            })
                        });
                        let data = await res.json();
                        if (res.ok && data.status === 'success') {
                            this.placedOrderNumber = data.order_number;
                            this.placedRedirectUrl = data.redirect_url || '{{ route('customer.account') }}';
                            this.cartItems = [];
                            this.saveCart();
                            this.cartOpen = false;
                            this.orderSuccessModal = true;
                            this.step = 'phone';
                            this.otpCode = '';
                        } else {
                            alert(data.message || 'Unable to place order. Please try again.');
                        }
                    } catch (e) {
                        alert('Failed to submit order. Please check your connection or contact WhatsApp.');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            };
        }
    </script>

    @stack('scripts')
</body>

</html>