@extends('layouts.app')

@section('meta_title', $product->product_name . ' - Wholesale Carton / Box Price | PatSons Foods Goa')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->description ?? 'Order authentic Goan ' . $product->product_name . ' in wholesale cartons/boxes direct from Dev Can Fruit Products, Morjim factory.'), 155))
@section('meta_keywords', $product->product_name . ', ' . $product->category . ', Goan wholesale boxes, PatSons Foods Morjim, wholesale Goa syrups cartons')
@section('og_image', (!empty($product->image) && $product->image !== 'default-product.webp') ? asset('images/products/' . $product->image) : asset('images/LOGO.webp'))
@section('og_type', 'product')

@push('schema')
    @php
        $defaultUnits = (int) ($product->units_per_box ?? 12);
        $minBoxPrice = $variants->isNotEmpty()
            ? $variants->min(fn($v) => (float) $v->price * (int) ($v->units_per_box ?? 12))
            : ((float) $product->price * $defaultUnits);
        $maxBoxPrice = $variants->isNotEmpty()
            ? $variants->max(fn($v) => (float) $v->price * (int) ($v->units_per_box ?? 12))
            : ((float) $product->price * $defaultUnits);

        $schemaData = [
            "@context" => "https://schema.org",
            "@graph" => [
                [
                    "@type" => "BreadcrumbList",
                    "itemListElement" => [
                        [
                            "@type" => "ListItem",
                            "position" => 1,
                            "name" => "Home",
                            "item" => route('home')
                        ],
                        [
                            "@type" => "ListItem",
                            "position" => 2,
                            "name" => "Shop",
                            "item" => route('shop')
                        ],
                        [
                            "@type" => "ListItem",
                            "position" => 3,
                            "name" => $product->category,
                            "item" => route('shop', ['category' => $product->category])
                        ],
                        [
                            "@type" => "ListItem",
                            "position" => 4,
                            "name" => $product->product_name,
                            "item" => url()->current()
                        ]
                    ]
                ],
                [
                    "@type" => "Product",
                    "@id" => url()->current() . "#product",
                    "name" => $product->product_name,
                    "image" => [
                        (!empty($product->image) && $product->image !== 'default-product.webp') ? asset('images/products/' . $product->image) : asset('images/LOGO.webp')
                    ],
                    "description" => strip_tags($product->description ?? $product->product_name),
                    "brand" => [
                        "@type" => "Brand",
                        "name" => "PatSons Foods"
                    ],
                    "manufacturer" => [
                        "@type" => "Organization",
                        "name" => "Dev Can Fruit Products, Morjim Goa"
                    ],
                    "category" => $product->category,
                    "offers" => [
                        "@type" => "AggregateOffer",
                        "priceCurrency" => "INR",
                        "lowPrice" => number_format((float) $minBoxPrice, 2, '.', ''),
                        "highPrice" => number_format((float) $maxBoxPrice, 2, '.', ''),
                        "offerCount" => (string) max(1, $variants->count()),
                        "availability" => "https://schema.org/InStock",
                        "seller" => [
                            "@type" => "Organization",
                            "name" => "PatSons Foods"
                        ]
                    ]
                ]
            ]
        ];
    @endphp
    <script type="application/ld+json">
                {!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
            </script>
@endpush

@section('content')
    @php
        $adminWa = \App\Models\Setting::get('whatsapp_admin_number', config('services.whatsapp.admin_number', '917758943614'));
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8" x-data="productViewer()"
        x-on:keydown.arrow-right.window="nextImage()" x-on:keydown.arrow-left.window="prevImage()"
        x-on:keydown.escape.window="lightboxOpen = false">

        <!-- Navigation Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-stone-200">
            <div class="flex items-center gap-2.5 flex-wrap text-xs">
                <button type="button"
                    onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ route('shop') }}'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-stone-100 border border-stone-200 text-stone-700 font-bold shadow-2xs hover:text-green-900 transition active:scale-95 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Back</span>
                </button>

                <nav class="flex items-center gap-1.5 font-semibold text-stone-500 overflow-x-auto py-1 scrollbar-none">
                    <a href="{{ route('home') }}"
                        class="hover:text-green-800 transition px-1.5 py-0.5 rounded hover:bg-stone-100">Home</a>
                    <span class="text-stone-300">/</span>
                    <a href="{{ route('shop') }}"
                        class="hover:text-green-800 transition px-1.5 py-0.5 rounded hover:bg-stone-100">Shop</a>
                    <span class="text-stone-300">/</span>
                    <a href="{{ route('shop', ['category' => $product->category]) }}"
                        class="hover:text-green-800 transition px-2 py-0.5 rounded-md bg-stone-100 hover:bg-stone-200 text-stone-700">
                        {{ $product->category }}
                    </a>
                    <span class="text-stone-300">/</span>
                    <span class="text-stone-900 font-bold truncate max-w-[180px] sm:max-w-xs">
                        {{ $product->product_name }}
                    </span>
                </nav>
            </div>

            <div class="hidden sm:flex items-center">
                <a href="{{ route('shop', ['category' => $product->category]) }}"
                    class="text-[11px] font-bold text-green-900 hover:text-green-700 hover:underline flex items-center gap-1">
                    <span>View all in {{ $product->category }}</span>
                    <span>→</span>
                </a>
            </div>
        </div>

        <div
            class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 bg-white rounded-3xl p-6 sm:p-10 border border-gray-100 shadow-xs">

            <!-- Product Media Stage -->
            <div class="lg:col-span-6 flex flex-col gap-4">
                <div class="relative w-full h-96 sm:h-[480px] rounded-3xl bg-gradient-to-b from-stone-50/90 via-amber-50/40 to-stone-100/90 border border-stone-200/60 overflow-hidden flex items-center justify-center p-6 group cursor-zoom-in"
                    x-on:click="lightboxOpen = true">

                    <div class="absolute w-52 h-52 rounded-full bg-amber-400/20 blur-3xl pointer-events-none"></div>

                    <img :src="'/images/products/' + currentImage" alt="{{ $product->product_name }}"
                        onerror="this.src='{{ asset('images/products/default-product.webp') }}'"
                        class="relative z-10 max-h-84 sm:max-h-[420px] max-w-full object-contain filter drop-shadow-[0_16px_18px_rgba(0,0,0,0.18)] transition-all duration-300 transform group-hover:scale-105">

                    <div class="absolute top-4 left-4 z-20 flex flex-col gap-1.5">
                        @if($product->is_featured)
                            <span
                                class="bg-amber-400 text-gray-950 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full shadow-2xs">
                                ★ Best Seller
                            </span>
                        @endif
                        <span
                            class="bg-emerald-900/90 backdrop-blur-md text-white text-[9px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow-2xs">
                            📦 Wholesale Carton Only
                        </span>
                        <template x-if="gallery && gallery.length > 1">
                            <span
                                class="bg-black/60 backdrop-blur-md text-white text-[9px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full"
                                x-text="(activeImageIndex + 1) + ' / ' + gallery.length + ' Angles'"></span>
                        </template>
                    </div>

                    <div
                        class="absolute top-4 right-4 bg-white/90 backdrop-blur-xs text-gray-700 text-[10px] font-bold px-3 py-1.5 rounded-full opacity-0 group-hover:opacity-100 transition shadow-xs z-20 flex items-center gap-1">
                        <span>🔍</span> Click to zoom
                    </div>

                    <template x-if="gallery && gallery.length > 1">
                        <div
                            class="absolute inset-x-3 inset-y-0 flex items-center justify-between z-20 pointer-events-none">
                            <button type="button" x-on:click.stop="prevImage()"
                                class="pointer-events-auto w-9 h-9 rounded-full bg-white/90 hover:bg-white text-gray-900 shadow-md flex items-center justify-center text-lg font-black transition-all hover:scale-110 active:scale-95">
                                ‹
                            </button>
                            <button type="button" x-on:click.stop="nextImage()"
                                class="pointer-events-auto w-9 h-9 rounded-full bg-white/90 hover:bg-white text-gray-900 shadow-md flex items-center justify-center text-lg font-black transition-all hover:scale-110 active:scale-95">
                                ›
                            </button>
                        </div>
                    </template>

                    <div x-show="addedToast" x-cloak x-transition
                        class="absolute inset-0 bg-green-900/90 backdrop-blur-xs flex items-center justify-center text-white font-bold text-sm z-30 rounded-3xl">
                        ✓ Added Wholesale Box to Quotation Cart!
                    </div>
                </div>

                <!-- Thumbnails Strip -->
                <template x-if="gallery && gallery.length > 1">
                    <div class="flex gap-2.5 overflow-x-auto py-1 scrollbar-none">
                        <template x-for="(img, idx) in gallery" :key="idx">
                            <button type="button" x-on:click="setImage(idx)"
                                :class="activeImageIndex === idx ? 'border-green-800 ring-2 ring-green-800 scale-95 shadow-sm' : 'border-gray-200 bg-gray-50 opacity-70 hover:opacity-100'"
                                class="relative w-16 h-16 sm:w-20 sm:h-20 rounded-2xl border-2 p-1.5 bg-white overflow-hidden shrink-0 transition-all flex items-center justify-center cursor-pointer">
                                <img :src="'/images/products/' + img" :alt="'Angle ' + (idx + 1)"
                                    onerror="this.src='{{ asset('images/products/default-product.webp') }}'"
                                    class="max-h-full max-w-full object-contain">
                            </button>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Product Info & Actions -->
            <div class="lg:col-span-6 flex flex-col justify-between space-y-6">
                <div>
                    <span
                        class="inline-block bg-green-100 text-green-900 text-[10px] font-extrabold uppercase tracking-widest px-3 py-1 rounded-md mb-2">
                        {{ $product->category }}
                    </span>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 tracking-tight leading-tight">
                        {{ $product->product_name }}
                    </h1>

                    <p class="text-xs sm:text-sm text-gray-600 mt-3 leading-relaxed font-medium">
                        {{ $product->description }}
                    </p>

                    <!-- Wholesale Box Price Card -->
                    <div
                        class="mt-6 p-4.5 rounded-2xl bg-stone-50 border border-stone-200/70 flex items-center justify-between gap-4">
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">
                                Wholesale Price (Per Box)
                            </span>
                            <div class="flex items-baseline gap-2 mt-0.5">
                                <span class="text-3xl font-black text-green-900">
                                    ₹<span x-text="boxPrice.toFixed(2)"></span>
                                </span>
                                <span class="text-xs text-gray-500 font-semibold">
                                    (₹<span x-text="Number(selectedPrice).toFixed(2)"></span> / piece)
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span
                                class="text-xs font-black text-emerald-900 bg-emerald-100/90 border border-emerald-300 px-3 py-1.5 rounded-xl inline-flex items-center gap-1.5">
                                <span>📦</span>
                                <span>Box of <span x-text="selectedUnitsPerBox"></span> Units</span>
                            </span>
                            <span class="block text-[10px] text-gray-400 font-bold uppercase mt-1">
                                Wholesale Carton Only
                            </span>
                        </div>
                    </div>

                    <!-- Select Box Variant -->
                    <div class="mt-6">
                        <label class="block text-xs font-extrabold text-gray-800 uppercase tracking-wider mb-2.5">
                            Select Packaging & Box Configuration:
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <template x-for="v in variants" :key="v.id">
                                <button type="button" x-on:click="setVariant(v)"
                                    :class="selectedId === v.id ? 'border-green-800 bg-green-50 text-green-900 font-black ring-2 ring-green-800 shadow-2xs' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'"
                                    class="border rounded-2xl p-3 text-left transition flex items-center justify-between cursor-pointer">
                                    <div>
                                        <span x-text="v.size" class="font-bold text-xs block text-gray-900"></span>
                                        <span class="text-[11px] text-gray-500 font-medium">
                                            <span x-text="v.units_per_box || 12"></span> units / carton
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-black text-green-900 block">
                                            ₹<span
                                                x-text="(Number(v.price) * Number(v.units_per_box || 12)).toFixed(2)"></span>
                                        </span>
                                        <span class="text-[10px] text-gray-400 font-bold">per box</span>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Call to Actions -->
                <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row gap-3">
                    <button type="button" x-on:click="triggerAdd()"
                        class="flex-1 bg-green-800 hover:bg-green-900 active:scale-98 text-white font-extrabold py-3.5 px-6 rounded-2xl text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>+ Add Box to Quotation Cart</span>
                    </button>

                    <a :href="'https://wa.me/{{ $adminWa }}?text=' + encodeURIComponent('Hello PatSons Goa! Inquiring for wholesale boxes of: {{ addslashes($product->product_name) }} (' + selectedSize + ', Box of ' + selectedUnitsPerBox + ') - ₹' + boxPrice.toFixed(2) + ' per box')"
                        target="_blank" rel="noopener noreferrer"
                        class="bg-amber-400 hover:bg-amber-300 active:scale-98 text-gray-950 font-black py-3.5 px-6 rounded-2xl text-xs sm:text-sm shadow transition flex items-center justify-center gap-1.5 text-center cursor-pointer">
                        <span>💬 Instant WhatsApp Quote</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Factory Quality & Manufacturer Highlights -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
            <div class="bg-white p-5 rounded-3xl border border-stone-200/70 shadow-2xs">
                <span class="text-xl block mb-1.5">🏭</span>
                <h3 class="font-extrabold text-stone-900">Direct From Morjim Factory</h3>
                <p class="text-stone-500 mt-1 leading-relaxed">
                    Manufactured by <strong>Dev Can Fruit Products</strong>, Morjim, Pernem, Goa[cite: 2]. Direct wholesale
                    carton distribution across Goa and India[cite: 1, 2].
                </p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-stone-200/70 shadow-2xs">
                <span class="text-xl block mb-1.5">📜</span>
                <h3 class="font-extrabold text-stone-900">FSSAI & GST Compliant</h3>
                <p class="text-stone-500 mt-1 leading-relaxed">
                    FSSAI Central Lic. <strong>10613001000730</strong>[cite: 2] & GSTIN
                    <strong>30BAGPP8577A1ZS</strong>[cite: 2]. Verified pure ingredients with 36 years experience[cite: 2].
                </p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-stone-200/70 shadow-2xs">
                <span class="text-xl block mb-1.5">📦</span>
                <h3 class="font-extrabold text-stone-900">Heavy Corrugated Box Packing</h3>
                <p class="text-stone-500 mt-1 leading-relaxed">
                    Hygienically packaged in factory-sealed cartons for safe HoReCa, catering, distributor, and retail
                    transit[cite: 1, 2].
                </p>
            </div>
        </div>

        <!-- Zoom Lightbox -->
        <div x-show="lightboxOpen" x-cloak
            class="fixed inset-0 z-50 bg-black/50 backdrop-blur-md flex items-center justify-center p-3 sm:p-6"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <button type="button" x-on:click="lightboxOpen = false"
                class="absolute top-4 right-4 sm:top-6 sm:right-6 bg-white/30 hover:bg-white text-white hover:text-black w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-black text-xl transition z-50 shadow-lg cursor-pointer backdrop-blur-xs">
                &times;
            </button>

            <div class="relative max-w-5xl max-h-[92vh] w-full flex items-center justify-center p-2"
                x-on:click.away="lightboxOpen = false">

                <button type="button" x-show="gallery && gallery.length > 1" x-on:click.stop="prevImage()"
                    class="absolute left-2 sm:left-4 z-40 w-11 h-11 sm:w-14 sm:h-14 rounded-full bg-white/20 hover:bg-white text-white hover:text-black shadow-2xl flex items-center justify-center text-2xl font-black transition-all backdrop-blur-md active:scale-95 cursor-pointer">
                    ‹
                </button>

                <img :src="'/images/products/' + currentImage" alt="{{ $product->product_name }}"
                    onerror="this.src='{{ asset('images/products/default-product.webp') }}'"
                    class="max-w-full max-h-[86vh] object-contain rounded-2xl drop-shadow-[0_25px_30px_rgba(0,0,0,0.5)] select-none">

                <button type="button" x-show="gallery && gallery.length > 1" x-on:click.stop="nextImage()"
                    class="absolute right-2 sm:right-4 z-40 w-11 h-11 sm:w-14 sm:h-14 rounded-full bg-white/20 hover:bg-white text-white hover:text-black shadow-2xl flex items-center justify-center text-2xl font-black transition-all backdrop-blur-md active:scale-95 cursor-pointer">
                    ›
                </button>

                <div x-show="gallery && gallery.length > 1"
                    class="absolute bottom-4 bg-black/60 backdrop-blur-md text-white text-xs font-black px-4 py-1.5 rounded-full shadow-lg"
                    x-text="(activeImageIndex + 1) + ' / ' + gallery.length">
                </div>
            </div>
        </div>

    </div>

    <script>
        function productViewer() {
            return {
                selectedId: {{ (int) $product->id }},
                selectedPrice: {{ (float) $product->price }},
                selectedUnitsPerBox: {{ (int) ($product->units_per_box ?? 12) }},
                selectedSize: @json($product->size),
                variants: @json($variants),
                gallery: @json($galleryImages ?? []),
                activeImageIndex: 0,
                addedToast: false,
                lightboxOpen: false,

                get boxPrice() {
                    return Number(this.selectedPrice) * Number(this.selectedUnitsPerBox || 12);
                },

                get currentImage() {
                    if (this.gallery && this.gallery.length > 0 && this.gallery[this.activeImageIndex]) {
                        return this.gallery[this.activeImageIndex];
                    }
                    return @json($product->image ?? 'default-product.webp');
                },

                nextImage() {
                    if (this.gallery && this.gallery.length > 0) {
                        this.activeImageIndex = (this.activeImageIndex + 1) % this.gallery.length;
                    }
                },

                prevImage() {
                    if (this.gallery && this.gallery.length > 0) {
                        this.activeImageIndex = (this.activeImageIndex - 1 + this.gallery.length) % this.gallery.length;
                    }
                },

                setImage(idx) {
                    this.activeImageIndex = idx;
                },

                setVariant(v) {
                    this.selectedId = v.id;
                    this.selectedPrice = Number(v.price);
                    this.selectedUnitsPerBox = Number(v.units_per_box || 12);
                    this.selectedSize = v.size;
                },

                triggerAdd() {
                    const calculatedBoxPrice = this.boxPrice;
                    const packLabel = this.selectedSize + ' (Box of ' + this.selectedUnitsPerBox + ')';

                    if (typeof addToCart === 'function') {
                        addToCart(this.selectedId, @json($product->product_name), calculatedBoxPrice, packLabel);
                    } else {
                        window.dispatchEvent(new CustomEvent('add-to-cart', {
                            detail: {
                                id: this.selectedId,
                                name: @json($product->product_name),
                                price: calculatedBoxPrice,
                                size: packLabel,
                                units_per_box: this.selectedUnitsPerBox,
                                image: this.currentImage
                            }
                        }));
                    }
                    this.addedToast = true;
                    setTimeout(() => this.addedToast = false, 1800);
                }
            };
        }
    </script>


@endsection