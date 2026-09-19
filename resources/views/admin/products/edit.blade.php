@extends('layouts.admin')

@section('content')
<div class="max-w-5xl space-y-6" x-data="{ 
    variants: {{ $variants->map(fn($v) => [
        'id' => $v->id, 
        'size' => $v->size, 
        'price' => (float)$v->price, 
        'units_per_box' => (int)($v->units_per_box ?? 12),
        'is_active' => (bool)$v->is_active
    ])->toJson() }} 
}">

    <!-- ==================== ENHANCED ADMIN NAVIGATION & HEADER ==================== -->
    <div class="bg-white p-4 sm:p-6 rounded-3xl border border-gray-200 shadow-xs space-y-4">
        <!-- Top Breadcrumbs & Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2 flex-wrap text-xs">
                <!-- Smart Back Button -->
                <a href="{{ route('admin.products') }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 font-bold shadow-2xs hover:text-green-900 transition active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Inventory</span>
                </a>

                <!-- Breadcrumb Links -->
                <nav class="flex items-center gap-1.5 font-semibold text-gray-500 overflow-x-auto py-0.5">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-green-800 transition px-1.5 py-0.5 rounded hover:bg-gray-100">
                        Dashboard
                    </a>
                    <span class="text-gray-300">/</span>
                    <a href="{{ route('admin.products') }}" class="hover:text-green-800 transition px-1.5 py-0.5 rounded hover:bg-gray-100">
                        Products Catalog
                    </a>
                    <span class="text-gray-300">/</span>
                    <span class="text-gray-900 font-bold truncate max-w-[180px] sm:max-w-xs">
                        {{ $product->product_name }}
                    </span>
                </nav>
            </div>

            <!-- View In Storefront Public Link -->
            <div class="flex items-center gap-2">
                <a href="{{ url('/product/' . $product->slug) }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 text-xs font-bold transition shadow-2xs">
                    <span>👁 View in Storefront</span>
                    <span class="text-[10px]">↗</span>
                </a>
            </div>
        </div>

        <!-- Title & In-Page Section Quick Jump Anchors -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                    <span>Edit: {{ $product->product_name }}</span>
                    @if($product->is_featured)
                        <span class="bg-amber-400 text-gray-950 text-[9px] font-black uppercase px-2 py-0.5 rounded-full">Best Seller</span>
                    @endif
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">Manage pricing variants, case packing counts, auto-approval thresholds, and product media.</p>
            </div>

            <!-- In-Page Quick Nav Jump Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 text-[11px] font-bold text-gray-600">
                <a href="#basic-info" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 transition shrink-0">General Info</a>
                <a href="#variants" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 transition shrink-0">Pack Sizes & Box Rates</a>
                <a href="#rules" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 transition shrink-0">Auto-Approval</a>
                <a href="#gallery" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 transition shrink-0">Media Gallery</a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold p-3.5 rounded-2xl flex items-center justify-between">
            <span>✓ {{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-700 font-bold">&times;</button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200 shadow-xs space-y-8">
        @csrf
        @method('PUT')

        <!-- Product Base Info -->
        <div id="basic-info" class="scroll-mt-24 space-y-5">
            <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                <span class="text-base">📝</span>
                <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">General Information</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Product Name *</label>
                    <input type="text" name="product_name" value="{{ old('product_name', $product->product_name) }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-bold outline-none focus:ring-2 focus:ring-green-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Category *</label>
                    <input type="text" name="category" list="category-list" value="{{ old('category', $product->category) }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-bold outline-none focus:ring-2 focus:ring-green-800">
                    <datalist id="category-list">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}"></option>
                        @endforeach
                    </datalist>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Description</label>
                <textarea name="description" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3.5 text-xs font-medium outline-none focus:ring-2 focus:ring-green-800">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>

        <hr class="border-gray-100">

        <!-- Pack Sizes, Units Per Box & Box Pricing Table -->
        <div id="variants" class="scroll-mt-24 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="text-base">📦</span>
                    <div>
                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">Wholesale Box & Pack Sizes</h3>
                        <p class="text-[11px] text-gray-500">Specify single unit price and number of units per carton to calculate wholesale box prices automatically.</p>
                    </div>
                </div>
                <button type="button" 
                        @click="variants.push({ id: null, size: '', price: 0.00, units_per_box: 12, is_active: true })" 
                        class="bg-gray-900 hover:bg-black text-white text-xs font-bold px-3 py-1.5 rounded-xl transition cursor-pointer">
                    + Add Size Variant
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(v, index) in variants" :key="index">
                    <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 p-3.5 bg-gray-50 border border-gray-200 rounded-2xl">
                        <input type="hidden" :name="'variants[' + index + '][id]'" :value="v.id">
                        
                        <!-- Size / Pack Name -->
                        <div class="flex-1 min-w-[180px]">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Pack Size Label</label>
                            <input type="text" :name="'variants[' + index + '][size]'" x-model="v.size" placeholder="e.g. 700ml Bottle, 500g Jar" required class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none focus:ring-2 focus:ring-green-800">
                        </div>

                        <!-- Single Piece Rate -->
                        <div class="w-28">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Price / Unit (₹)</label>
                            <input type="number" step="0.01" min="0" :name="'variants[' + index + '][price]'" x-model="v.price" required class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none focus:ring-2 focus:ring-green-800">
                        </div>

                        <!-- Multiplier: Units per Box -->
                        <div class="w-28">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Units / Box</label>
                            <input type="number" min="1" step="1" :name="'variants[' + index + '][units_per_box]'" x-model="v.units_per_box" required class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none focus:ring-2 focus:ring-green-800">
                        </div>

                        <!-- Calculated Box Total Preview -->
                        <div class="w-32 text-right self-center pt-2 sm:pt-0">
                            <span class="text-[10px] text-gray-400 block font-bold uppercase">Box Wholesale</span>
                            <span class="text-xs font-black text-green-900">
                                ₹<span x-text="(Number(v.price || 0) * Number(v.units_per_box || 1)).toFixed(2)"></span>
                            </span>
                        </div>

                        <!-- Remove Row -->
                        <div class="pt-2 sm:pt-4">
                            <button type="button" @click="variants.splice(index, 1)" x-show="variants.length > 1" class="text-red-500 hover:text-red-700 text-xs font-bold p-1 cursor-pointer" title="Remove Variant">
                                ✕
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <hr class="border-gray-100">

        <!-- Individual Product Auto-Approval Override -->
        <div id="rules" class="scroll-mt-24 bg-stone-50/80 p-5 rounded-2xl border border-stone-200/80 space-y-4">
            <div class="flex items-center gap-2 pb-2 border-b border-stone-200">
                <span class="text-base">⚡</span>
                <div>
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">Individual Auto-Approval Rules</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">Configure custom order approval quantity thresholds specific to this product.</p>
                </div>
            </div>

            <div class="space-y-3.5">
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="auto_approve_override" value="1" 
                        {{ old('auto_approve_override', $product->auto_approve_override) ? 'checked' : '' }}
                        class="w-4 h-4 text-green-800 rounded-md border-gray-300 focus:ring-green-700">
                    <span class="text-xs font-bold text-gray-800">Override global store threshold for this specific product</span>
                </label>

                <div class="max-w-xs">
                    <label class="block text-[10px] font-black text-gray-600 uppercase mb-1">
                        Custom Min. Boxes for Auto-Approval
                    </label>
                    <input type="number" name="auto_approve_min_qty" 
                        value="{{ old('auto_approve_min_qty', $product->auto_approve_min_qty) }}" 
                        placeholder="e.g. 5 (Leave blank for global setting)" min="1"
                        class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-bold outline-none focus:ring-2 focus:ring-green-800">
                </div>
                <p class="text-[11px] text-gray-500">
                    If enabled, quotations containing at least this quantity of boxes for this item will automatically be set to <strong>Approved</strong>.
                </p>
            </div>
        </div>

        <hr class="border-gray-100">

        <!-- Unified Photo Gallery Section -->
        <div id="gallery" class="scroll-mt-24 space-y-4">
            <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                <span class="text-base">🖼️</span>
                <div>
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">Shared Photo Gallery (Auto-Discovered Variants)</h3>
                    <p class="text-[11px] text-gray-500">Tick the photos you want displayed in the customer storefront. All pack sizes share this gallery.</p>
                </div>
            </div>

            @if(!empty($allFoundImages))
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-4">
                    @foreach($allFoundImages as $img)
                        @php
                            $isAssigned = in_array($img, $assignedImages) || ($product->image === $img);
                            $isPrimary = ($product->image === $img);

                            $src = null;
                            if (Str::startsWith($img, ['http://', 'https://'])) {
                                $src = $img;
                            } elseif (file_exists(public_path('storage/' . $img))) {
                                $src = asset('storage/' . $img);
                            } elseif (file_exists(public_path('images/products/' . $img))) {
                                $src = asset('images/products/' . $img);
                            } elseif (file_exists(public_path('images/' . $img))) {
                                $src = asset('images/' . $img);
                            } elseif (file_exists(public_path($img))) {
                                $src = asset($img);
                            } else {
                                $src = asset('storage/' . $img);
                            }
                        @endphp

                        <div class="rounded-2xl border p-2.5 space-y-2 flex flex-col justify-between transition {{ $isAssigned ? 'border-green-300 bg-green-50/40' : 'border-gray-200 bg-gray-50 opacity-75' }}">
                            <div class="w-full h-32 rounded-xl overflow-hidden bg-white border border-gray-200 relative">
                                <img src="{{ $src }}" 
                                     alt="{{ $product->product_name }}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.onerror=null; this.src='{{ asset('images/default-product.webp') }}';">

                                @if($isPrimary)
                                    <span class="absolute top-2 left-2 bg-green-900 text-white text-[9px] font-black px-2 py-0.5 rounded-md shadow-xs">
                                        ★ Cover
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-1.5 pt-1">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox" 
                                           name="visible_images[]" 
                                           value="{{ $img }}" 
                                           {{ $isAssigned ? 'checked' : '' }} 
                                           class="w-4 h-4 text-green-800 rounded-md border-gray-300 focus:ring-green-700">
                                    <span class="text-[11px] font-bold {{ $isAssigned ? 'text-green-900' : 'text-gray-600' }}">Show in Shop</span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer select-none text-[10px] text-gray-500 font-bold">
                                    <input type="radio" 
                                           name="primary_choice" 
                                           value="{{ $img }}" 
                                           {{ $isPrimary ? 'checked' : '' }} 
                                           class="text-green-800 focus:ring-green-700">
                                    <span>Set as Cover</span>
                                </label>
                            </div>

                            <div class="text-[9px] font-mono text-gray-500 truncate" title="{{ basename($img) }}">
                                {{ basename($img) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Upload New Cover Photo</label>
                    <input type="file" name="primary_image" accept="image/*" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-2.5 text-xs text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-green-100 file:text-green-800 hover:file:bg-green-200">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Upload Additional Gallery Photos</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-2.5 text-xs text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200">
                </div>
            </div>
        </div>

        <hr class="border-gray-100">

        <!-- Store Visibility Toggles -->
        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="w-4 h-4 text-green-800 rounded-md border-gray-300 focus:ring-green-700">
                <span class="text-xs font-bold text-gray-800">Featured / Best Seller</span>
            </label>
        </div>

        <div class="flex justify-between items-center pt-4 border-t border-gray-100">
            <a href="{{ route('admin.products') }}" class="text-xs font-bold text-gray-500 hover:text-gray-800 hover:underline">
                Cancel & Return
            </a>

            <button type="submit" class="bg-green-800 hover:bg-green-700 text-white text-xs font-black px-8 py-3 rounded-2xl shadow-sm transition cursor-pointer">
                Save All Sizes & Media Updates →
            </button>
        </div>
    </form>

</div>
@endsection