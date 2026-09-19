@extends('layouts.app')

@section('meta_title', 'PatSons Foods Goa - Wholesale Factory Box Packing | Authentic Kokum, Jams & Syrups')
@section('meta_description', 'Wholesale cartons and boxes of pure Goan Kokum Agal, Cashew Fruit Jams, bar cordials, and fruit squashes direct from Dev Can Fruit Products, Morjim, Goa.')
@section('meta_keywords', 'PatSons Foods Goa, Goan Kokum Agal wholesale, Cashew Jam cartons, Kokum Syrup box pack, Morjim factory wholesale')

@section('content')
    @php
        $adminWa = \App\Models\Setting::get('whatsapp_admin_number', config('services.whatsapp.admin_number', '917758943614'));
    @endphp

    <div class="space-y-12 sm:space-y-16 pb-16">

        <!-- ==================== 1. HERO BANNER SECTION ==================== -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-2 sm:pt-4">
            <div
                class="relative overflow-hidden rounded-3xl shadow-xl border border-stone-200 bg-stone-900 min-h-[380px] sm:min-h-[460px] lg:min-h-[520px] flex items-center justify-start">

                <!-- Full High-Res Brand Banner Background -->
                <img src="{{ file_exists(public_path('images/products/default-product.webp')) ? asset('images/products/default-product.webp') : (file_exists(public_path('images/default-product.webp')) ? asset('images/default-product.webp') : (file_exists(public_path('images/products/default-product.jpg')) ? asset('images/products/default-product.jpg') : asset('images/default-product.jpg'))) }}"
                    alt="PatSons Foods Goa"
                    class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none select-none z-0">

                <!-- Subtle Dark Tint for Text Readability -->
                <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/50 to-transparent z-1"></div>

                <!-- Hero Content Overlay -->
                <div class="relative z-10 max-w-2xl p-6 sm:p-10 lg:p-14 space-y-4 sm:space-y-6 text-left">
                    <div
                        class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-md border border-white/25 px-3.5 py-1 rounded-full text-white text-[11px] font-bold tracking-wide">
                        <span>🌴 Est. 1990 • Dev Can Fruit Products, Morjim Goa</span>
                    </div>

                    <h1
                        class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight text-white drop-shadow-md">
                        Goodness of Nature
                        <span class="text-amber-400 block">In Wholesale Box Packs.</span>
                    </h1>

                    <p class="text-xs sm:text-base text-stone-100 font-medium leading-relaxed drop-shadow-sm max-w-lg">
                        Authentic Goan Kokum Agal, handcrafted Cashew Fruit Jams, and natural fruit squashes direct from the
                        Morjim factory. Exclusively supplied in sealed wholesale cartons and bulk packaging.
                    </p>

                    <!-- CTAs -->
                    <div class="pt-2 flex flex-wrap items-center gap-3">
                        <a href="{{ route('shop') }}"
                            class="bg-amber-400 hover:bg-amber-300 active:scale-95 text-stone-950 font-black px-7 py-3.5 rounded-2xl text-xs sm:text-sm shadow-lg transition-all flex items-center gap-2">
                            <span>📦 Explore Wholesale Catalog</span>
                            <span>→</span>
                        </a>
                        <a href="https://wa.me/{{ $adminWa }}?text=Hello%20PatSons%20Goa!%20I%20would%20like%20to%20inquire%20about%20your%20wholesale%20box%20pricing."
                            target="_blank"
                            class="bg-white/20 hover:bg-white/30 active:scale-95 text-white border border-white/30 font-black px-6 py-3.5 rounded-2xl text-xs sm:text-sm transition-all flex items-center gap-2 backdrop-blur-md">
                            <span>💬 WhatsApp Inquiries</span>
                        </a>
                    </div>
                </div>

            </div>
        </section>


        <!-- ==================== 2. SPECIALTY CATEGORIES ==================== -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-8">
                <span
                    class="text-[11px] font-extrabold text-green-900 uppercase tracking-widest bg-green-50 px-3.5 py-1 rounded-full">
                    Wholesale Catalog
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-2">
                    Browse By Category
                </h2>
            </div>

            @php
                $categories = [
                    ['name' => 'Kokum & Agal', 'icon' => '🥥', 'desc' => 'Pure Extracts', 'slug' => 'Kokum & Agal'],
                    ['name' => 'Cashew Jams', 'icon' => '🍯', 'desc' => 'Nutty Spreads', 'slug' => 'Cashew & Fruit Jams'],
                    ['name' => 'Squashes & Juices', 'icon' => '🍹', 'desc' => 'Amla, Jamun & Fruit', 'slug' => 'Squashes & Juices'],
                    ['name' => 'Syrups & Crushes', 'icon' => '🍧', 'desc' => 'Mocktails & Coolers', 'slug' => 'Syrups & Crushes'],
                    ['name' => 'Dry Fruits & Honey', 'icon' => '🥜', 'desc' => 'Gourmet Wellness', 'slug' => 'Dry Fruits & Honey'],
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                @foreach($categories as $category)
                    <a href="{{ route('shop', ['category' => $category['slug']]) }}"
                        class="group bg-white hover:bg-green-50/40 border border-gray-200 hover:border-green-300 p-5 rounded-2xl text-center shadow-xs hover:shadow-md transition-all flex flex-col justify-between items-center">
                        <span class="text-3xl sm:text-4xl block mb-2 group-hover:scale-110 transition-transform duration-300">
                            {{ $category['icon'] }}
                        </span>
                        <div>
                            <h3
                                class="font-black text-xs sm:text-sm text-gray-900 group-hover:text-green-900 transition-colors leading-tight">
                                {{ $category['name'] }}
                            </h3>
                            <span class="text-[10px] text-gray-400 group-hover:text-green-700 block mt-1 font-bold">
                                {{ $category['desc'] }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>


        <!-- ==================== 3. FEATURED PRODUCTS ==================== -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-6">
                <div>
                    <span
                        class="text-[11px] font-extrabold text-green-900 uppercase tracking-widest bg-green-50 px-3 py-1 rounded-full">
                        Carton Wholesale
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-1.5">
                        Signature Products
                    </h2>
                </div>
                <a href="{{ route('shop') }}"
                    class="text-xs sm:text-sm font-black text-green-900 hover:text-green-700 transition flex items-center gap-1">
                    <span>View All Boxes</span>
                    <span>→</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($featuredProducts->take(4) as $slug => $variants)
                    @php $primary = $variants->first(); @endphp
                    <div class="bg-white rounded-3xl border border-gray-200 hover:border-green-300 shadow-xs hover:shadow-xl transition-all duration-300 p-5 flex flex-col justify-between"
                        x-data="{
                                    selectedId: {{ $primary->id }},
                                    selectedPrice: {{ (float) $primary->price }},
                                    selectedUnitsPerBox: {{ (int) ($primary->units_per_box ?? 12) }},
                                    selectedSize: '{{ $primary->size }}',
                                    selectedImage: '{{ $primary->image ?? 'default-product.webp' }}',
                                    variants: {{ $variants->toJson() }},
                                    get boxPrice() {
                                        return Number(this.selectedPrice) * Number(this.selectedUnitsPerBox || 12);
                                    },
                                    updateVariant(e) {
                                        let found = this.variants.find(v => v.id == e.target.value);
                                        if (found) {
                                            this.selectedId = found.id;
                                            this.selectedPrice = Number(found.price);
                                            this.selectedUnitsPerBox = Number(found.units_per_box || 12);
                                            this.selectedSize = found.size;
                                            if (found.image) this.selectedImage = found.image;
                                        }
                                    },
                                    triggerAddToCart() {
                                        const finalBoxPrice = this.boxPrice;
                                        const label = this.selectedSize + ' (Box of ' + this.selectedUnitsPerBox + ')';
                                        if (typeof addToCart === 'function') {
                                            addToCart(this.selectedId, '{{ addslashes($primary->product_name) }}', finalBoxPrice, label);
                                        } else {
                                            window.dispatchEvent(new CustomEvent('add-to-cart', {
                                                detail: {
                                                    id: this.selectedId,
                                                    name: '{{ addslashes($primary->product_name) }}',
                                                    price: finalBoxPrice,
                                                    size: label,
                                                    units_per_box: this.selectedUnitsPerBox,
                                                    image: this.selectedImage
                                                }
                                            }));
                                        }
                                    }
                                }">
                        <div>
                            <a href="{{ route('catalog.show', $primary->slug) }}" class="block">
                                <div
                                    class="relative w-full h-52 bg-stone-50 rounded-2xl overflow-hidden mb-4 flex items-center justify-center p-4 border border-stone-200/50">
                                    <img :src="'/images/products/' + selectedImage" alt="{{ $primary->product_name }}"
                                        onerror="this.src='/images/products/default-product.webp'"
                                        class="max-h-44 max-w-full object-contain filter drop-shadow-sm transition-transform duration-300 hover:scale-105"
                                        loading="lazy">
                                    <span
                                        class="absolute top-2 left-2 bg-emerald-900/80 backdrop-blur-md text-white text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md">
                                        📦 Box Pack
                                    </span>
                                </div>
                            </a>

                            <span
                                class="inline-block bg-green-50 text-green-900 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded mb-1.5">
                                {{ $primary->category }}
                            </span>

                            <a href="{{ route('catalog.show', $primary->slug) }}">
                                <h3
                                    class="font-black text-gray-900 text-sm leading-snug hover:text-green-800 transition line-clamp-2">
                                    {{ $primary->product_name }}
                                </h3>
                            </a>

                            <!-- Box Packing Selector -->
                            <div class="mt-3">
                                <label class="block text-[10px] font-black text-gray-400 mb-1 uppercase tracking-wider">
                                    Carton Packaging
                                </label>
                                <select @change="updateVariant($event)"
                                    class="w-full bg-gray-50 border border-gray-200 text-gray-800 font-bold text-xs rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-green-800 cursor-pointer">
                                    @foreach($variants as $variant)
                                        @php
                                            $uBox = (int) ($variant->units_per_box ?? 12);
                                            $totalBoxPrice = (float) $variant->price * $uBox;
                                        @endphp
                                        <option value="{{ $variant->id }}">
                                            {{ $variant->size }} (Box of {{ $uBox }}) — ₹{{ number_format($totalBoxPrice, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Wholesale Box Price & Add -->
                        <div class="mt-4 pt-3.5 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="text-[9px] text-gray-400 font-bold uppercase block">Per Box (<span
                                        x-text="selectedUnitsPerBox"></span> pcs)</span>
                                <span class="text-xl font-black text-green-900">₹<span
                                        x-text="boxPrice.toFixed(2)"></span></span>
                            </div>
                            <button @click="triggerAddToCart()"
                                class="bg-green-900 hover:bg-green-800 active:scale-95 text-white text-xs font-black px-3.5 py-2.5 rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                                <span>+ Add Box</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>


        <!-- ==================== 4. HOW ORDERING WORKS ==================== -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-stone-100/70 border border-stone-200 rounded-3xl p-6 sm:p-10">
                <div class="text-center max-w-xl mx-auto mb-8">
                    <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                        Simple Wholesale Ordering
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Get sealed factory cartons direct from our Morjim facility in 3
                        steps.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                    <div class="space-y-2 p-4 bg-white rounded-2xl border border-stone-200/60 shadow-xs">
                        <div
                            class="w-10 h-10 bg-green-900 text-white font-black rounded-xl flex items-center justify-center mx-auto text-sm">
                            1
                        </div>
                        <h3 class="font-bold text-sm text-gray-900">Choose Carton Packs</h3>
                        <p class="text-xs text-gray-500">Select box sizes and carton quantities in your quotation cart.</p>
                    </div>

                    <div class="space-y-2 p-4 bg-white rounded-2xl border border-stone-200/60 shadow-xs">
                        <div
                            class="w-10 h-10 bg-green-900 text-white font-black rounded-xl flex items-center justify-center mx-auto text-sm">
                            2
                        </div>
                        <h3 class="font-bold text-sm text-gray-900">Verify via WhatsApp</h3>
                        <p class="text-xs text-gray-500">Enter your business WhatsApp number to receive an instant
                            verification code.
                        </p>
                    </div>

                    <div class="space-y-2 p-4 bg-white rounded-2xl border border-stone-200/60 shadow-xs">
                        <div
                            class="w-10 h-10 bg-green-900 text-white font-black rounded-xl flex items-center justify-center mx-auto text-sm">
                            3
                        </div>
                        <h3 class="font-bold text-sm text-gray-900">Direct Dispatch</h3>
                        <p class="text-xs text-gray-500">Factory confirms batch availability, commercial invoice, and
                            transport dispatch.</p>
                    </div>
                </div>
            </div>
        </section>


        <!-- ==================== 5. BULK INQUIRIES ==================== -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="relative overflow-hidden bg-green-950 text-white rounded-3xl p-6 sm:p-10 shadow-xl border border-green-900 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-1.5 text-center md:text-left">
                    <span class="text-amber-400 text-[10px] font-black uppercase tracking-wider">
                        Wholesale & Food Service Supply
                    </span>
                    <h3 class="text-xl sm:text-2xl font-black text-white">Master Cartons & Commercial 35L Drums</h3>
                    <p class="text-xs text-stone-300 max-w-lg">
                        Direct wholesale rates on Kokum Agal, Appetizer Crush, and Syrups for hotels, supermarkets,
                        caterers, and distributors across Goa.
                    </p>
                </div>
                <a href="https://wa.me/{{ $adminWa }}?text=Hello%20PatSons!%20I%20am%20interested%20in%20Bulk%20Wholesale%20Carton%20pricing."
                    target="_blank"
                    class="bg-amber-400 hover:bg-amber-300 text-stone-950 font-black px-6 py-3.5 rounded-2xl text-xs sm:text-sm shadow-md transition shrink-0">
                    Inquire Wholesale Cartons →
                </a>
            </div>
        </section>

    </div>
@endsection