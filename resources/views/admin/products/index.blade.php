@extends('layouts.admin')

@section('content')
    <div class="space-y-6 pb-12" x-data="productTableManager()" x-cloak>

        <!-- Top Section: Metric KPI Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Catalog SKUs</span>
                    <div class="text-xl sm:text-2xl font-black text-gray-900 mt-1 flex items-baseline gap-2">
                        <span>{{ $totalCount }}</span>
                        <span class="text-xs font-bold text-gray-500">({{ $totalSkus }} Variants)</span>
                    </div>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-800 flex items-center justify-center text-xl shrink-0">
                    📦
                </div>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Store Visibility</span>
                    <div class="text-xl sm:text-2xl font-black text-emerald-700 mt-1 flex items-baseline gap-2">
                        <span>{{ $liveCount }}</span>
                        <span class="text-xs font-bold text-gray-400">Live / {{ $hiddenCount }} Hidden</span>
                    </div>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-800 flex items-center justify-center text-xl shrink-0">
                    🟢
                </div>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Factory Stock Health</span>
                    <div class="text-xl sm:text-2xl font-black text-gray-900 mt-1 flex items-baseline gap-2">
                        <span>{{ $inStockCount }}</span>
                        <span class="text-xs font-bold {{ $outOfStockCount > 0 ? 'text-rose-600' : 'text-gray-400' }}">
                            @if($outOfStockCount > 0)
                                (⚠️ {{ $outOfStockCount }} Out of Stock)
                            @else
                                (All In Stock)
                            @endif
                        </span>
                    </div>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center text-xl shrink-0">
                    🏭
                </div>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Featured Best Sellers</span>
                    <div class="text-xl sm:text-2xl font-black text-amber-600 mt-1">
                        {{ $featuredCount }}
                    </div>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                    ★
                </div>
            </div>
        </div>

        <!-- Header Bar: Title & Primary Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Product Catalog & Wholesale Inventory</h1>
                <p class="text-xs text-gray-500 mt-0.5">Manage live storefront pricing, case packaging units, stock availability, and media.</p>
            </div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('admin.products.export') }}"
                    class="bg-white hover:bg-gray-100 text-gray-800 border border-gray-300 text-xs font-black px-4 py-2.5 rounded-xl shadow-2xs transition inline-flex items-center gap-2 cursor-pointer"
                    title="Export wholesale catalog to Excel / CSV">
                    <span>📥</span>
                    <span>Export CSV</span>
                </a>
                <a href="{{ route('admin.products.create') }}"
                    class="bg-green-800 hover:bg-green-700 text-white text-xs font-black px-4 py-2.5 rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer">
                    <span class="text-base leading-none">+</span>
                    <span>Add Product SKU</span>
                </a>
            </div>
        </div>

        <!-- Search, Filter & Per-Page Controls -->
        <div class="bg-white p-3.5 rounded-2xl border border-gray-200 shadow-xs space-y-3">
            <div class="flex flex-col md:flex-row gap-3 items-center justify-between">
                <!-- Instant Client-Side Live Search -->
                <div class="relative flex-1 w-full">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 text-sm">🔍</span>
                    <input type="text" x-model="clientSearch" @input="onSearchChange()"
                        placeholder="Instant search by product name, category, pack size..."
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-900 outline-none focus:ring-2 focus:ring-green-800">
                    <button x-show="clientSearch.length > 0" @click="clientSearch = ''; onSearchChange();"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-700 text-xs font-bold">
                        ✕
                    </button>
                </div>

                <!-- Status Filter Dropdown & Items Per Page -->
                <div class="flex items-center gap-2.5 w-full md:w-auto justify-end">
                    <select x-model="statusFilter" @change="onStatusFilterChange()"
                        class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-800 outline-none focus:ring-2 focus:ring-green-800 cursor-pointer">
                        <option value="">All Statuses</option>
                        <option value="active">🟢 Live in Store</option>
                        <option value="hidden">⚪ Hidden</option>
                        <option value="in_stock">📦 In Stock</option>
                        <option value="out_of_stock">⚠️ Out of Stock</option>
                        <option value="featured">★ Featured Best Seller</option>
                    </select>

                    <div class="flex items-center gap-1.5 text-xs font-bold text-gray-500 shrink-0">
                        <span>Show:</span>
                        <select x-model.number="perPage" @change="onPerPageChange()"
                            class="bg-gray-50 border border-gray-200 rounded-xl px-2.5 py-2 text-xs font-bold text-gray-800 outline-none focus:ring-2 focus:ring-green-800 cursor-pointer">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="9999">All</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Category Navigation Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none border-t border-gray-100 pt-2.5">
                <button type="button" @click="setCategory('all')"
                    :class="activeCategory === 'all'
                        ? 'bg-green-950 text-white font-black shadow-xs ring-1 ring-green-900'
                        : 'bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold border border-gray-200'"
                    class="px-3.5 py-1.5 rounded-xl text-xs flex items-center gap-1.5 transition shrink-0 cursor-pointer">
                    <span>🗂️ All</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full"
                        :class="activeCategory === 'all' ? 'bg-amber-400 text-gray-950 font-black' : 'bg-gray-200/80 text-gray-700'">
                        {{ $groupedProducts->count() }}
                    </span>
                </button>

                @foreach($categories as $cat)
                    @php
                        $catCount = $groupedProducts->filter(fn($g) => $g->first()->category === $cat)->count();
                    @endphp
                    <button type="button" @click="setCategory('{{ addslashes($cat) }}')"
                        :class="activeCategory === '{{ addslashes($cat) }}'
                            ? 'bg-green-950 text-white font-black shadow-xs ring-1 ring-green-900'
                            : 'bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold border border-gray-200'"
                        class="px-3.5 py-1.5 rounded-xl text-xs flex items-center gap-1.5 transition shrink-0 cursor-pointer">
                        <span>{{ $cat }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full"
                            :class="activeCategory === '{{ addslashes($cat) }}' ? 'bg-amber-400 text-gray-950 font-black' : 'bg-gray-200/80 text-gray-700'">
                            {{ $catCount }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Sticky Floating Bulk Action Toolbar -->
        <div x-show="selectedSlugs.length > 0" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="sticky top-2 z-20 bg-gray-900 text-white p-3 rounded-2xl shadow-xl border border-gray-700 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="bg-green-800 text-white text-xs font-black px-2.5 py-1 rounded-lg">
                    <span x-text="selectedSlugs.length"></span> Selected
                </span>
                <span class="text-xs text-gray-300 font-bold hidden sm:inline">Apply bulk update to all selected products:</span>
            </div>

            <div class="flex items-center gap-2 flex-wrap text-xs font-bold">
                <button type="button" @click="executeBulkAction('activate')"
                    class="bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-1.5 rounded-xl transition cursor-pointer">
                    🟢 Make Live
                </button>
                <button type="button" @click="executeBulkAction('deactivate')"
                    class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-1.5 rounded-xl transition cursor-pointer">
                    ⚪ Hide
                </button>
                <button type="button" @click="executeBulkAction('mark_in_stock')"
                    class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded-xl transition cursor-pointer">
                    📦 Mark In Stock
                </button>
                <button type="button" @click="executeBulkAction('mark_out_of_stock')"
                    class="bg-amber-600 hover:bg-amber-500 text-white px-3 py-1.5 rounded-xl transition cursor-pointer">
                    ⚠️ Out of Stock
                </button>
                <button type="button" @click="executeBulkAction('feature')"
                    class="bg-amber-400 hover:bg-amber-300 text-gray-950 font-black px-3 py-1.5 rounded-xl transition cursor-pointer">
                    ★ Feature
                </button>
                <button type="button" @click="executeBulkAction('delete')"
                    class="bg-rose-700 hover:bg-rose-600 text-white px-3 py-1.5 rounded-xl transition cursor-pointer">
                    🗑️ Delete
                </button>
                <button type="button" @click="selectedSlugs = []"
                    class="text-gray-400 hover:text-white px-2 py-1 transition cursor-pointer">
                    Deselect All ✕
                </button>
            </div>
        </div>

        <!-- Products Table Container -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">
                                <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected"
                                    class="w-4 h-4 rounded text-green-800 border-gray-300 focus:ring-green-700 cursor-pointer">
                            </th>
                            <th class="py-3 px-4 w-20">Photo</th>
                            <th class="py-3 px-4">Product Name & Category</th>
                            <th class="py-3 px-4">Pack Sizes & Rates</th>
                            <th class="py-3 px-4 text-center w-36">Stock Status</th>
                            <th class="py-3 px-4 text-center w-36">Storefront</th>
                            <th class="py-3 px-4 text-center w-28">Featured</th>
                            <th class="py-3 px-4 text-right w-44">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs font-medium">
                        @forelse($groupedProducts as $slug => $items)
                            @php
                                $p = $items->first();
                                $rawImg = $p->image;
                                $isDefault = empty($rawImg) || $rawImg === 'default-product.webp';
                                $imgSrc = $p->image_url;

                                $totalGalleryCount = 0;
                                foreach ($items as $it) {
                                    if (is_array($it->images)) {
                                        $totalGalleryCount += count($it->images);
                                    }
                                }

                                $isInStock = $items->every(fn($v) => ($v->in_stock ?? true) != false);
                                $isFeatured = $items->contains(fn($v) => (bool)$v->is_featured);
                                $isActive = (bool)$p->is_active;
                            @endphp

                            <tr class="hover:bg-gray-50/80 transition product-row"
                                data-slug="{{ $slug }}"
                                data-category="{{ $p->category }}"
                                data-search="{{ strtolower($p->product_name . ' ' . $p->category . ' ' . $items->pluck('size')->join(' ')) }}"
                                data-is-active="{{ $isActive ? '1' : '0' }}"
                                data-in-stock="{{ $isInStock ? '1' : '0' }}"
                                data-is-featured="{{ $isFeatured ? '1' : '0' }}"
                                x-show="isRowVisible($el)"
                                :class="selectedSlugs.includes('{{ $slug }}') ? 'bg-green-50/40' : ''">

                                <!-- Checkbox -->
                                <td class="py-3 px-4 text-center">
                                    <input type="checkbox" value="{{ $slug }}" x-model="selectedSlugs"
                                        class="w-4 h-4 rounded text-green-800 border-gray-300 focus:ring-green-700 cursor-pointer">
                                </td>

                                <!-- Transparent Product Thumbnail -->
                                <td class="py-3 px-4">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden bg-gradient-to-b from-stone-50 via-amber-50/10 to-stone-100/60 border border-gray-200 relative flex items-center justify-center p-1.5 shadow-2xs">
                                        @if(!$isDefault && $imgSrc)
                                            <img src="{{ $imgSrc }}" alt="{{ $p->product_name }}"
                                                class="max-h-full max-w-full object-contain filter drop-shadow-[0_4px_6px_rgba(0,0,0,0.12)] transition transform hover:scale-110"
                                                onerror="this.onerror=null; this.src='{{ asset('images/default-product.webp') }}';"
                                                loading="lazy">
                                            @if($totalGalleryCount > 0)
                                                <span class="absolute bottom-1 right-1 bg-black/70 text-white text-[9px] px-1.5 py-0.2 rounded-md font-black">
                                                    +{{ $totalGalleryCount }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-2xl opacity-40">🥫</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Product Info -->
                                <td class="py-3 px-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.products.edit', $p->id) }}"
                                                class="font-black text-gray-900 text-sm hover:text-green-800 transition">
                                                {{ $p->product_name }}
                                            </a>
                                            <a href="{{ url('/products/' . $slug) }}" target="_blank"
                                                class="text-gray-400 hover:text-green-800 text-[11px] font-bold inline-flex items-center gap-0.5"
                                                title="View live product on customer storefront">
                                                <span>👁️</span>
                                                <span class="underline">View</span>
                                            </a>
                                        </div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md">
                                                {{ $p->category }}
                                            </span>
                                            @if(!empty($p->vyapar_item_id))
                                                <span class="text-[9px] font-mono font-bold text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.2 rounded">
                                                    Vyapar: #{{ $p->vyapar_item_id }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Pack Sizes, Pricing & Box Details -->
                                <td class="py-3 px-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($items as $variant)
                                            <div class="inline-flex items-center gap-1.5 bg-gray-50 border border-gray-200/90 text-gray-800 text-[11px] font-bold px-2.5 py-1 rounded-lg">
                                                <span>{{ $variant->size }}</span>
                                                <span class="text-green-800 font-black">₹{{ number_format($variant->price, 0) }}</span>
                                                <span class="text-[10px] text-gray-400">({{ $variant->units_per_box ?: 12 }}/box)</span>
                                                <button type="button"
                                                    @click="openQuickEdit({{ $variant->id }}, '{{ addslashes($p->product_name) }}', '{{ addslashes($variant->size) }}', {{ $variant->price }}, {{ $variant->units_per_box ?: 12 }})"
                                                    class="text-gray-400 hover:text-green-700 ml-0.5 cursor-pointer text-xs"
                                                    title="Quick Edit Price & Units">
                                                    ✏️
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>

                                <!-- Stock Status AJAX Toggle -->
                                <td class="py-3 px-4 text-center">
                                    <button type="button"
                                        @click="toggleStock({{ $p->id }}, '{{ $slug }}')"
                                        :id="'stock-btn-' + '{{ $slug }}'"
                                        class="text-xs font-black px-3 py-1.5 rounded-xl transition border cursor-pointer {{ $isInStock ? 'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}">
                                        {{ $isInStock ? '🟢 In Stock' : '🔴 Sold Out' }}
                                    </button>
                                </td>

                                <!-- Storefront Visibility AJAX Toggle -->
                                <td class="py-3 px-4 text-center">
                                    <button type="button"
                                        @click="toggleStatus({{ $p->id }}, '{{ $slug }}')"
                                        :id="'status-btn-' + '{{ $slug }}'"
                                        class="text-xs font-black px-3 py-1.5 rounded-xl transition border cursor-pointer {{ $isActive ? 'bg-green-50 text-green-900 border-green-200 hover:bg-green-100' : 'bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200' }}">
                                        {{ $isActive ? '🟢 Live' : '⚪ Hidden' }}
                                    </button>
                                </td>

                                <!-- Featured Star AJAX Toggle -->
                                <td class="py-3 px-4 text-center">
                                    <button type="button"
                                        @click="toggleFeatured({{ $p->id }}, '{{ $slug }}')"
                                        :id="'featured-btn-' + '{{ $slug }}'"
                                        class="text-xs font-black px-2.5 py-1.5 rounded-xl transition border cursor-pointer {{ $isFeatured ? 'bg-amber-50 text-amber-800 border-amber-300 hover:bg-amber-100' : 'bg-gray-50 text-gray-400 border-gray-200 hover:bg-gray-100' }}"
                                        title="Toggle Featured / Best Seller">
                                        {{ $isFeatured ? '★ Best Seller' : '☆ Regular' }}
                                    </button>
                                </td>

                                <!-- Actions -->
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.products.edit', $p->id) }}"
                                            class="bg-gray-100 hover:bg-green-800 hover:text-white text-gray-700 font-bold text-xs px-3 py-1.5 rounded-lg border border-gray-200 transition inline-flex items-center gap-1 cursor-pointer"
                                            title="Edit sizes, photos, and descriptions">
                                            <span>Edit</span>
                                        </a>

                                        <form method="POST" action="{{ route('admin.products.duplicate', $p->id) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="bg-gray-100 hover:bg-blue-600 hover:text-white text-gray-700 font-bold text-xs px-2.5 py-1.5 rounded-lg border border-gray-200 transition cursor-pointer"
                                                title="Duplicate product as new variant draft"
                                                onclick="return confirm('Duplicate this product to create a new draft variant?')">
                                                📋 Clone
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.products.delete', $p->id) }}" class="inline"
                                            onsubmit="return confirm('Permanently delete {{ addslashes($p->product_name) }} and all associated sizes?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-gray-100 hover:bg-rose-700 hover:text-white text-gray-500 font-bold text-xs p-1.5 rounded-lg border border-gray-200 transition cursor-pointer"
                                                title="Delete product">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-400 italic text-sm">
                                    No products matching your search or filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Footer: Pagination & Record Summary -->
            <div class="p-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-bold text-gray-600">
                <div>
                    Showing <span class="text-gray-900 font-black" x-text="totalFilteredItems > 0 ? ((currentPage - 1) * perPage) + 1 : 0"></span>
                    to <span class="text-gray-900 font-black" x-text="Math.min(currentPage * perPage, totalFilteredItems)"></span>
                    of <span class="text-gray-900 font-black" x-text="totalFilteredItems"></span> matching products
                    <span x-show="totalFilteredItems !== {{ $groupedProducts->count() }}" class="text-gray-400 font-normal">
                        (filtered from {{ $groupedProducts->count() }} total)
                    </span>
                </div>

                <div class="flex items-center gap-1.5 flex-wrap">
                    <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                        class="px-3 py-1.5 rounded-xl border bg-white border-gray-200 hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed transition">
                        ‹ Prev
                    </button>

                    <template x-for="p in visiblePages" :key="p">
                        <button type="button" @click="setPage(p)"
                            :class="currentPage === p ? 'bg-green-950 text-white font-black shadow-xs ring-1 ring-green-900' : 'bg-white hover:bg-gray-100 text-gray-700 border border-gray-200'"
                            class="w-8 h-8 rounded-xl flex items-center justify-center transition cursor-pointer"
                            x-text="p">
                        </button>
                    </template>

                    <button type="button" @click="nextPage()" :disabled="currentPage === totalPages"
                        class="px-3 py-1.5 rounded-xl border bg-white border-gray-200 hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed transition">
                        Next ›
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Price & Box Units Edit Modal -->
        <div x-show="quickEdit.open" x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="quickEdit.open = false"
                class="bg-white rounded-3xl max-w-sm w-full p-6 space-y-4 shadow-2xl border border-gray-200">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="font-black text-sm text-gray-900">Quick Edit Variant</h3>
                        <p class="text-[11px] text-gray-500" x-text="quickEdit.productName + ' (' + quickEdit.size + ')'"></p>
                    </div>
                    <button @click="quickEdit.open = false" class="text-gray-400 hover:text-gray-700 font-bold">&times;</button>
                </div>

                <div class="space-y-3 text-xs font-bold">
                    <div>
                        <label class="block uppercase text-gray-500 mb-1">Price per Unit (₹)</label>
                        <input type="number" step="0.01" min="0" x-model.number="quickEdit.price"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl p-2.5 text-gray-900 outline-none focus:ring-2 focus:ring-green-800">
                    </div>

                    <div>
                        <label class="block uppercase text-gray-500 mb-1">Units per Case / Box</label>
                        <input type="number" min="1" x-model.number="quickEdit.unitsPerBox"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl p-2.5 text-gray-900 outline-none focus:ring-2 focus:ring-green-800">
                    </div>

                    <div class="p-3 bg-emerald-50 rounded-xl text-emerald-900 text-[11px]">
                        Estimated Box Price: <strong x-text="'₹' + (quickEdit.price * quickEdit.unitsPerBox).toFixed(2)"></strong>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="button" @click="quickEdit.open = false"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 rounded-xl font-bold transition">
                        Cancel
                    </button>
                    <button type="button" @click="saveQuickEdit()"
                        class="flex-1 bg-green-800 hover:bg-green-700 text-white py-2.5 rounded-xl font-black shadow transition">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        <!-- Floating Instant Action Toast Notification -->
        <div x-show="toast.show" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
            class="fixed bottom-6 right-6 z-50 bg-gray-900 text-white px-4 py-3 rounded-2xl shadow-2xl border border-gray-700 flex items-center gap-3 text-xs font-bold">
            <span x-text="toast.message"></span>
            <button @click="toast.show = false" class="text-gray-400 hover:text-white">&times;</button>
        </div>

    </div>

    <!-- Alpine Product Table Manager Script -->
    <script>
        function productTableManager() {
            return {
                clientSearch: '',
                activeCategory: sessionStorage.getItem('admin_active_category') || '{{ !empty($category) ? $category : 'all' }}',
                statusFilter: '{{ !empty($statusFilter) ? $statusFilter : '' }}',
                currentPage: parseInt(sessionStorage.getItem('admin_current_page') || '1'),
                perPage: parseInt(localStorage.getItem('admin_per_page') || '10'),
                selectedSlugs: [],
                toast: {
                    show: false,
                    message: ''
                },
                quickEdit: {
                    open: false,
                    id: null,
                    productName: '',
                    size: '',
                    price: 0,
                    unitsPerBox: 12
                },

                get allRowElements() {
                    return Array.from(document.querySelectorAll('.product-row'));
                },

                get filteredRows() {
                    const search = this.clientSearch.trim().toLowerCase();
                    return this.allRowElements.filter(row => {
                        // 1. Category check
                        if (this.activeCategory !== 'all' && row.getAttribute('data-category') !== this.activeCategory) {
                            return false;
                        }

                        // 2. Status check
                        if (this.statusFilter === 'active' && row.getAttribute('data-is-active') !== '1') return false;
                        if (this.statusFilter === 'hidden' && row.getAttribute('data-is-active') !== '0') return false;
                        if (this.statusFilter === 'in_stock' && row.getAttribute('data-in-stock') !== '1') return false;
                        if (this.statusFilter === 'out_of_stock' && row.getAttribute('data-in-stock') !== '0') return false;
                        if (this.statusFilter === 'featured' && row.getAttribute('data-is-featured') !== '1') return false;

                        // 3. Search check
                        if (search.length > 0) {
                            const rowSearch = row.getAttribute('data-search') || '';
                            if (!rowSearch.includes(search)) {
                                return false;
                            }
                        }

                        return true;
                    });
                },

                get totalFilteredItems() {
                    return this.filteredRows.length;
                },

                get totalPages() {
                    return Math.max(1, Math.ceil(this.totalFilteredItems / this.perPage));
                },

                get isAllSelected() {
                    if (this.filteredRows.length === 0) return false;
                    const visibleSlugs = this.filteredRows.map(r => r.getAttribute('data-slug'));
                    return visibleSlugs.every(s => this.selectedSlugs.includes(s));
                },

                get visiblePages() {
                    let pages = [];
                    let start = Math.max(1, this.currentPage - 2);
                    let end = Math.min(this.totalPages, start + 4);
                    if (end - start < 4) {
                        start = Math.max(1, end - 4);
                    }
                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                    return pages;
                },

                onSearchChange() {
                    this.currentPage = 1;
                },

                onStatusFilterChange() {
                    this.currentPage = 1;
                },

                onPerPageChange() {
                    localStorage.setItem('admin_per_page', this.perPage.toString());
                    this.currentPage = 1;
                },

                setCategory(cat) {
                    this.activeCategory = cat;
                    this.currentPage = 1;
                    sessionStorage.setItem('admin_active_category', cat);
                    sessionStorage.setItem('admin_current_page', '1');
                },

                setPage(p) {
                    this.currentPage = p;
                    sessionStorage.setItem('admin_current_page', p.toString());
                },

                prevPage() {
                    if (this.currentPage > 1) {
                        this.currentPage--;
                        sessionStorage.setItem('admin_current_page', this.currentPage.toString());
                    }
                },

                nextPage() {
                    if (this.currentPage < this.totalPages) {
                        this.currentPage++;
                        sessionStorage.setItem('admin_current_page', this.currentPage.toString());
                    }
                },

                isRowVisible(el) {
                    const matchIdx = this.filteredRows.indexOf(el);
                    if (matchIdx === -1) return false;

                    const startIndex = (this.currentPage - 1) * this.perPage;
                    const endIndex = startIndex + this.perPage;
                    return matchIdx >= startIndex && matchIdx < endIndex;
                },

                toggleSelectAll(e) {
                    const visibleSlugs = this.filteredRows.map(r => r.getAttribute('data-slug'));
                    if (e.target.checked) {
                        this.selectedSlugs = Array.from(new Set([...this.selectedSlugs, ...visibleSlugs]));
                    } else {
                        this.selectedSlugs = this.selectedSlugs.filter(s => !visibleSlugs.includes(s));
                    }
                },

                triggerToast(message) {
                    this.toast.message = message;
                    this.toast.show = true;
                    setTimeout(() => { this.toast.show = false; }, 3500);
                },

                // 1-Click AJAX Toggle: Storefront Live / Hidden
                async toggleStatus(id, slug) {
                    try {
                        const res = await fetch(`/ps-control-morjim/products/${id}/toggle-status`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            const btn = document.getElementById('status-btn-' + slug);
                            const row = document.querySelector(`[data-slug="${slug}"]`);
                            if (btn && row) {
                                if (data.is_active) {
                                    btn.className = 'text-xs font-black px-3 py-1.5 rounded-xl transition border cursor-pointer bg-green-50 text-green-900 border-green-200 hover:bg-green-100';
                                    btn.innerText = '🟢 Live';
                                    row.setAttribute('data-is-active', '1');
                                } else {
                                    btn.className = 'text-xs font-black px-3 py-1.5 rounded-xl transition border cursor-pointer bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200';
                                    btn.innerText = '⚪ Hidden';
                                    row.setAttribute('data-is-active', '0');
                                }
                            }
                            this.triggerToast(data.message || 'Updated status');
                        }
                    } catch (e) {
                        alert('Could not update status: ' + e.message);
                    }
                },

                // 1-Click AJAX Toggle: Stock Available / Sold Out
                async toggleStock(id, slug) {
                    try {
                        const res = await fetch(`/ps-control-morjim/products/${id}/toggle-stock`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            const btn = document.getElementById('stock-btn-' + slug);
                            const row = document.querySelector(`[data-slug="${slug}"]`);
                            if (btn && row) {
                                if (data.in_stock) {
                                    btn.className = 'text-xs font-black px-3 py-1.5 rounded-xl transition border cursor-pointer bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100';
                                    btn.innerText = '🟢 In Stock';
                                    row.setAttribute('data-in-stock', '1');
                                } else {
                                    btn.className = 'text-xs font-black px-3 py-1.5 rounded-xl transition border cursor-pointer bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100';
                                    btn.innerText = '🔴 Sold Out';
                                    row.setAttribute('data-in-stock', '0');
                                }
                            }
                            this.triggerToast(data.message || 'Stock updated');
                        }
                    } catch (e) {
                        alert('Could not update stock: ' + e.message);
                    }
                },

                // 1-Click AJAX Toggle: Featured Star
                async toggleFeatured(id, slug) {
                    try {
                        const res = await fetch(`/ps-control-morjim/products/${id}/toggle-featured`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            const btn = document.getElementById('featured-btn-' + slug);
                            const row = document.querySelector(`[data-slug="${slug}"]`);
                            if (btn && row) {
                                if (data.is_featured) {
                                    btn.className = 'text-xs font-black px-2.5 py-1.5 rounded-xl transition border cursor-pointer bg-amber-50 text-amber-800 border-amber-300 hover:bg-amber-100';
                                    btn.innerText = '★ Best Seller';
                                    row.setAttribute('data-is-featured', '1');
                                } else {
                                    btn.className = 'text-xs font-black px-2.5 py-1.5 rounded-xl transition border cursor-pointer bg-gray-50 text-gray-400 border-gray-200 hover:bg-gray-100';
                                    btn.innerText = '☆ Regular';
                                    row.setAttribute('data-is-featured', '0');
                                }
                            }
                            this.triggerToast(data.message || 'Featured tag updated');
                        }
                    } catch (e) {
                        alert('Could not update featured: ' + e.message);
                    }
                },

                // Open Quick Edit Modal
                openQuickEdit(id, productName, size, price, units) {
                    this.quickEdit = {
                        open: true,
                        id: id,
                        productName: productName,
                        size: size,
                        price: parseFloat(price) || 0,
                        unitsPerBox: parseInt(units) || 12
                    };
                },

                // Save Quick Edit
                async saveQuickEdit() {
                    try {
                        const res = await fetch(`/ps-control-morjim/products/${this.quickEdit.id}/quick-update`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                price: this.quickEdit.price,
                                units_per_box: this.quickEdit.unitsPerBox
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.quickEdit.open = false;
                            this.triggerToast(data.message);
                            setTimeout(() => { window.location.reload(); }, 600);
                        }
                    } catch (e) {
                        alert('Quick update failed: ' + e.message);
                    }
                },

                // Execute Bulk Actions
                async executeBulkAction(action) {
                    if (this.selectedSlugs.length === 0) return;

                    if (action === 'delete') {
                        if (!confirm(`Are you sure you want to permanently delete ${this.selectedSlugs.length} selected products? This action cannot be undone.`)) {
                            return;
                        }
                    }

                    try {
                        const res = await fetch('{{ route('admin.products.bulkAction') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                action: action,
                                slugs: this.selectedSlugs
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.triggerToast(data.message);
                            setTimeout(() => { window.location.reload(); }, 600);
                        }
                    } catch (e) {
                        alert('Bulk action failed: ' + e.message);
                    }
                }
            };
        }
    </script>
@endsection