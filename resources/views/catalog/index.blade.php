@extends('layouts.app')
@php
    $catParam = request('category');
    $canonicalUrl = $catParam ? route('shop', ['category' => $catParam]) : route('shop');
    $catTitle = $catParam ? $catParam . ' - Wholesale Goan Box Packs | PatSons Foods Goa' : 'Wholesale Goan Kokum, Jams, Syrups & Crushes Catalog | PatSons Foods';
    $catDesc = $catParam 
        ? 'Browse wholesale carton packs and boxes of ' . $catParam . ' direct from Dev Can Fruit Products factory in Morjim, Pernem - Goa. FSSAI certified authentic Goan recipes.'
        : 'Browse our complete catalog of authentic Goan Kokum Agal, Cashew Fruit Jams, bar cordials, squashes, and appetizer crushes direct from Morjim factory.';
@endphp

@section('meta_title', $catTitle)
@section('meta_description', $catDesc)
@section('meta_keywords', 'PatSons Foods catalog, wholesale Goan Kokum Agal, Cashew Jam wholesale, bar cordials Goa, Dev Can Fruit Products Morjim, authentic Goan food supply')
@section('canonical', $canonicalUrl)
@section('title', $catTitle)

@push('schema')
@php
    $breadcrumbs = [
        [
            "@type" => "ListItem",
            "position" => 1,
            "name" => "Home",
            "item" => route('home')
        ],
        [
            "@type" => "ListItem",
            "position" => 2,
            "name" => "Catalog",
            "item" => route('shop')
        ]
    ];

    if ($catParam) {
        $breadcrumbs[] = [
            "@type" => "ListItem",
            "position" => 3,
            "name" => $catParam,
            "item" => $canonicalUrl
        ];
    }

    $itemList = [];
    $pos = 1;
    foreach ($products->take(24) as $slug => $variants) {
        $firstItem = $variants->first();
        if ($firstItem) {
            $itemList[] = [
                "@type" => "ListItem",
                "position" => $pos++,
                "url" => route('catalog.show', $firstItem->slug),
                "name" => $firstItem->product_name
            ];
        }
    }

    $collectionSchema = [
        "@context" => "https://schema.org",
        "@graph" => [
            [
                "@type" => "BreadcrumbList",
                "itemListElement" => $breadcrumbs
            ],
            [
                "@type" => "CollectionPage",
                "name" => $catTitle,
                "description" => $catDesc,
                "url" => $canonicalUrl
            ],
            [
                "@type" => "ItemList",
                "name" => $catTitle,
                "numberOfItems" => count($itemList),
                "itemListElement" => $itemList
            ]
        ]
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($collectionSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
    @php
        $adminWa = \App\Models\Setting::get('whatsapp_admin_number', config('services.whatsapp.admin_number', '917758943614'));
    @endphp

    <div class="space-y-8 sm:space-y-12 pb-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">

        <!-- ==================== HEADER & SEARCH BAR ==================== -->
        <div
            class="bg-gradient-to-br from-green-950 via-green-900 to-emerald-950 rounded-3xl p-6 sm:p-10 text-white shadow-xl relative overflow-hidden">
            <div
                class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none">
            </div>
            <div class="absolute -left-20 -top-20 w-80 h-80 rounded-full bg-amber-500/10 blur-3xl pointer-events-none">
            </div>

            <div class="relative z-10 max-w-2xl space-y-3 sm:space-y-4">
                <span
                    class="inline-block bg-amber-400 text-gray-950 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full shadow-xs">
                    Direct From Morjim, Goa Factory
                </span>
                <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white leading-tight">
                    Authentic Goan Food Catalog
                </h1>
                <p class="text-xs sm:text-sm text-green-100/80 leading-relaxed font-medium">
                    Traditional Kokum Agal, handcrafted Cashew Fruit Jams, herbal digestifs, and refreshing squashes.
                </p>

                <!-- Search Form -->
                <form method="GET" action="{{ route('shop') }}" class="pt-2 flex gap-2">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by product name, ingredient, or pack size..."
                            class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-4 py-3 sm:py-3.5 text-xs sm:text-sm text-white placeholder-green-200/60 font-bold outline-none focus:ring-2 focus:ring-amber-400 transition shadow-inner">
                        @if(request('search'))
                            <a href="{{ route('shop', array_merge(request()->except('search'))) }}"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-white/60 hover:text-white font-black text-sm">
                                &times;
                            </a>
                        @endif
                    </div>
                    <button type="submit"
                        class="bg-amber-400 hover:bg-amber-300 text-gray-950 font-black px-6 py-3 rounded-2xl text-xs sm:text-sm shadow-md transition shrink-0">
                        Search
                    </button>
                </form>
            </div>
        </div>

        <!-- ==================== HORIZONTAL CATEGORY NAV PILLS ==================== -->
        <div class="sticky top-2 z-30 bg-gray-50/90 backdrop-blur-md py-2.5 -mx-4 px-4 sm:mx-0 sm:px-0">
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none snap-x">
                <a href="{{ route('shop', array_merge(request()->except('category'))) }}"
                    class="snap-start shrink-0 px-4 py-2 rounded-2xl text-xs font-black transition-all border {{ !request('category') ? 'bg-green-900 text-white border-green-900 shadow-sm' : 'bg-white text-gray-700 border-gray-200 hover:border-gray-300' }}">
                    🌿 All Products ({{ $products->count() }})
                </a>

                @foreach($categories as $cat)
                    <a href="{{ route('shop', array_merge(request()->except('category'), ['category' => $cat])) }}"
                        class="snap-start shrink-0 px-4 py-2 rounded-2xl text-xs font-black transition-all border {{ request('category') === $cat ? 'bg-green-900 text-white border-green-900 shadow-sm' : 'bg-white text-gray-700 border-gray-200 hover:border-gray-300' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- ==================== PRODUCT GRID ==================== -->
        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 sm:gap-6">
                @foreach($products as $slug => $variants)
                    @php 
                                                                    $primary = $variants->first();
                        $gallery = !empty($primary->gallery_images) ? $primary->gallery_images : [$primary->image ?? 'default-product.webp'];
                    @endphp

                    <div class="group bg-white rounded-3xl border border-gray-200 hover:border-emerald-300 shadow-2xs hover:shadow-xl transition-all duration-300 p-4 sm:p-5 flex flex-col justify-between"
                        x-data="{
                                                             selectedId: {{ $primary->id }},
                                                             selectedPrice: {{ $primary->price }},
                                                             selectedSize: '{{ $primary->size }}',
                                                             selectedImage: '{{ $primary->image ?? 'default-product.webp' }}',
                                                             variants: {{ $variants->toJson() }},
                                                             setVariant(v) {
                                                                 this.selectedId = v.id;
                                                                 this.selectedPrice = v.price;
                                                                 this.selectedSize = v.size;
                                                                 if (v.image) this.selectedImage = v.image;
                                                             }
                                                         }">

                        <div>
                            <!-- Large Image Showcase Frame with Multi-Angle Indicator -->
                            <a href="{{ route('catalog.show', $primary->slug) }}" class="block relative group">
                                <div
                                    class="relative w-full h-56 sm:h-64 bg-gradient-to-b from-stone-50 via-amber-50/20 to-stone-100/90 rounded-2xl overflow-hidden mb-3 flex items-center justify-center p-4 border border-stone-200/60">

                                    <img :src="'/images/products/' + selectedImage"
                                        alt="{{ $primary->product_name }} - Wholesale Goan Product"
                                        onerror="this.src='/images/products/default-product.webp'"
                                        width="400" height="400"
                                        class="max-h-48 sm:max-h-56 max-w-full object-contain filter drop-shadow-[0_12px_14px_rgba(0,0,0,0.14)] transition-transform duration-500 group-hover:scale-105"
                                        loading="lazy">

                                    <!-- Badges -->
                                    <div class="absolute top-2.5 left-2.5 flex flex-col gap-1 z-10">
                                        @if($primary->is_featured)
                                            <span
                                                class="bg-amber-400 text-gray-950 text-[9px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow-xs">
                                                ★ Best Seller
                                            </span>
                                        @endif
                                    </div>

                                    @if(count($gallery) > 1)
                                        <span
                                            class="absolute bottom-2.5 right-2.5 bg-black/60 backdrop-blur-xs text-white text-[9px] font-black px-2 py-0.5 rounded-md">
                                            {{ count($gallery) }} Photos 📸
                                        </span>
                                    @endif
                                </div>
                            </a>

                            <!-- Category & Title -->
                            <span
                                class="inline-block bg-green-50 text-green-900 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md mb-1">
                                {{ $primary->category }}
                            </span>

                            <a href="{{ route('catalog.show', $primary->slug) }}" class="block">
                                <h3
                                    class="font-black text-gray-900 text-sm sm:text-base leading-snug hover:text-green-800 transition line-clamp-2">
                                    {{ $primary->product_name }}
                                </h3>
                            </a>

                            <!-- Pack Sizes Pills (Tap to Switch Size & Price) -->
                            <div class="mt-3 space-y-1">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-wider block">Available
                                    Sizes:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="v in variants" :key="v.id">
                                        <button type="button" @click="setVariant(v)"
                                            :class="selectedId === v.id ? 'bg-green-900 text-white border-green-900 shadow-2xs font-black' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100 font-bold'"
                                            class="px-2.5 py-1 rounded-xl text-[10px] border transition flex items-center gap-1 cursor-pointer">
                                            <span x-text="v.size"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Price Row -->
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <div class="flex items-baseline justify-between mb-3">
                                <div>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase block leading-none">Price per unit</span>
                                    <span class="text-xl font-black text-green-950">₹<span
                                            x-text="Number(selectedPrice).toFixed(2)"></span></span>
                                </div>
                                <span class="text-[10px] text-emerald-700 font-black bg-emerald-50 px-2 py-0.5 rounded-md">
                                    ● In Stock
                                </span>
                            </div>

                            <!-- CTA Buttons: Ordering commented out for view-only catalog display -->
                            {{--
                            <div class="flex items-center gap-2">
                                <button type="button"
                                    @click="$dispatch('add-to-cart', { id: selectedId, name: '{{ addslashes($primary->product_name) }}', price: selectedPrice, size: selectedSize, image: selectedImage })"
                                    class="flex-1 bg-green-900 hover:bg-green-800 text-white font-black text-xs py-2.5 px-3 rounded-xl shadow-xs transition active:scale-95 flex items-center justify-center gap-1.5 cursor-pointer">
                                    <span>🛍️</span>
                                    <span>+ Add to Cart</span>
                                </button>
                                <a :href="'https://wa.me/{{ $adminPhone }}?text=' + encodeURIComponent('Hello PatSons! I want to inquire about: {{ addslashes($primary->product_name) }} (' + selectedSize + ') - ₹' + Number(selectedPrice).toFixed(2) + ' per unit.')"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 text-emerald-800 font-black text-xs py-2.5 px-3 rounded-xl transition active:scale-95 flex items-center justify-center gap-1 cursor-pointer"
                                    title="WhatsApp Quote">
                                    <span>💬</span>
                                    <span class="hidden sm:inline">Quote</span>
                                </a>
                            </div>
                            --}}
                            <a href="{{ route('catalog.show', $primary->slug) }}"
                                class="w-full bg-stone-100 hover:bg-green-900 hover:text-white text-stone-800 font-black text-xs py-2.5 px-3 rounded-xl transition text-center flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>View Product Details</span>
                                <span>→</span>
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-3xl border border-gray-200 p-12 text-center max-w-md mx-auto space-y-4 shadow-xs">
                <span class="text-4xl block">🔍</span>
                <h3 class="text-lg font-black text-gray-900">No products found</h3>
                <p class="text-xs text-gray-500">We couldn't find any products matching your search criteria.</p>
                <a href="{{ route('shop') }}"
                    class="inline-block bg-green-900 text-white text-xs font-black px-6 py-2.5 rounded-xl">
                    Reset All Filters
                </a>
            </div>
        @endif

    </div>
@endsection