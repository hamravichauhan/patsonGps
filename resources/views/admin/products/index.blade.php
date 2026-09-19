@extends('layouts.admin')

@section('content')
    <div class="h-[calc(100vh-4rem)] flex flex-col justify-between space-y-4" x-data="productTableManager()">

        <!-- Top Section: Header, Alerts, Search & Category Tabs -->
        <div class="space-y-3 shrink-0">
            <!-- Top Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-gray-200">
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">Product Inventory & Catalog</h1>
                    <p class="text-sm text-gray-500">Fixed viewport page manager. Zero scroll view.</p>
                </div>
                <a href="{{ route('admin.products.create') }}"
                    class="bg-green-800 hover:bg-green-700 text-white text-sm font-black px-5 py-2.5 rounded-xl shadow-xs transition flex items-center gap-2 self-start sm:self-auto cursor-pointer">
                    <span class="text-lg leading-none">+</span>
                    <span>Add Product</span>
                </a>
            </div>

            @if(session('success'))
                <div
                    class="bg-green-50 border border-green-200 text-green-800 text-sm font-bold p-3 rounded-xl shadow-xs flex items-center justify-between">
                    <span>✓ {{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()"
                        class="text-green-700 hover:text-green-950 font-bold cursor-pointer">&times;</button>
                </div>
            @endif

            <!-- Search & Status Filter -->
            <form method="GET" action="{{ route('admin.products') }}"
                class="bg-white p-3 rounded-xl border border-gray-200 shadow-xs flex flex-wrap gap-3 items-center">
                <div class="flex-1 min-w-[220px]">
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Search product name, size, category..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-bold outline-none focus:ring-2 focus:ring-green-800">
                </div>

                <div class="min-w-[180px]">
                    <select name="status_filter"
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm font-bold outline-none focus:ring-2 focus:ring-green-800 cursor-pointer">
                        <option value="">All Shop Statuses</option>
                        <option value="active" {{ ($statusFilter ?? '') === 'active' ? 'selected' : '' }}>🟢 Live in Store
                        </option>
                        <option value="hidden" {{ ($statusFilter ?? '') === 'hidden' ? 'selected' : '' }}>⚪ Hidden Products
                        </option>
                    </select>
                </div>

                <button type="submit"
                    class="bg-gray-900 hover:bg-black text-white text-sm font-black px-5 py-2.5 rounded-lg transition shadow-xs cursor-pointer">
                    Filter
                </button>

                @if(!empty($search) || !empty($category) || !empty($statusFilter))
                    <a href="{{ route('admin.products', ['reset' => 1]) }}"
                        class="text-sm font-bold text-red-600 hover:underline px-2">
                        Clear ✕
                    </a>
                @endif
            </form>

            <!-- Category Navigation Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-gray-200">
                <button type="button" x-on:click="setCategory('all')" :class="activeCategory === 'all' 
                                ? 'bg-green-950 text-white font-black shadow-xs ring-1 ring-green-900' 
                                : 'bg-white hover:bg-gray-100 text-gray-700 font-bold border border-gray-200'"
                    class="px-4 py-2.5 rounded-lg text-sm flex items-center gap-2 transition shrink-0 cursor-pointer">
                    <span>🗂️ All Categories</span>
                    <span class="text-xs px-2 py-0.5 rounded-full"
                        :class="activeCategory === 'all' ? 'bg-amber-400 text-gray-950 font-black' : 'bg-gray-100 text-gray-600'">
                        {{ $groupedProducts->count() }}
                    </span>
                </button>

                @foreach($categories as $cat)
                    @php
                        $catCount = $groupedProducts->filter(fn($g) => $g->first()->category === $cat)->count();
                    @endphp
                    <button type="button" x-on:click="setCategory('{{ addslashes($cat) }}')" :class="activeCategory === '{{ addslashes($cat) }}' 
                                            ? 'bg-green-950 text-white font-black shadow-xs ring-1 ring-green-900' 
                                            : 'bg-white hover:bg-gray-100 text-gray-700 font-bold border border-gray-200'"
                        class="px-4 py-2.5 rounded-lg text-sm flex items-center gap-2 transition shrink-0 cursor-pointer">
                        <span>{{ $cat }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full"
                            :class="activeCategory === '{{ addslashes($cat) }}' ? 'bg-amber-400 text-gray-950 font-black' : 'bg-gray-100 text-gray-600'">
                            {{ $catCount }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Middle Section: Table Container (Increased Size) -->
        <div
            class="flex-1 bg-white rounded-2xl border border-gray-200 shadow-lg flex flex-col justify-between overflow-hidden min-h-0">
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left">
                    <thead
                        class="sticky top-0 z-10 bg-gray-50 border-b-2 border-gray-200 text-gray-600 font-bold uppercase tracking-wider text-sm">
                        <tr>
                            <th class="py-3.5 px-5 w-24">Photo</th>
                            <th class="py-3.5 px-5">Product Name & Category</th>
                            <th class="py-3.5 px-5">Pack Sizes & Pricing</th>
                            <th class="py-3.5 px-5 text-center w-44">Store Status</th>
                            <th class="py-3.5 px-5 text-right w-44">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium">
                        @forelse($groupedProducts as $slug => $items)
                            @php
                                $p = $items->first();
                                $rawImg = $p->image;
                                $isDefault = empty($rawImg) || $rawImg === 'default-product.webp';

                                $imgSrc = null;
                                if (!$isDefault) {
                                    if (Str::startsWith($rawImg, ['http://', 'https://'])) {
                                        $imgSrc = $rawImg;
                                    } elseif (file_exists(public_path('storage/' . $rawImg))) {
                                        $imgSrc = asset('storage/' . $rawImg);
                                    } elseif (file_exists(public_path('images/products/' . $rawImg))) {
                                        $imgSrc = asset('images/products/' . $rawImg);
                                    } elseif (file_exists(public_path('images/' . $rawImg))) {
                                        $imgSrc = asset('images/' . $rawImg);
                                    } elseif (file_exists(public_path($rawImg))) {
                                        $imgSrc = asset($rawImg);
                                    } else {
                                        $imgSrc = asset('storage/' . $rawImg);
                                    }
                                }

                                $totalGalleryCount = 0;
                                foreach ($items as $it) {
                                    if (is_array($it->images)) {
                                        $totalGalleryCount += count($it->images);
                                    }
                                }
                            @endphp

                            <tr class="hover:bg-gray-50/70 transition product-row" data-category="{{ $p->category }}"
                                x-show="isRowVisible($el)" x-cloak>

                                <!-- Thumbnail -->
                                <td class="py-3 px-5">
                                    <div
                                        class="w-20 h-20 rounded-2xl overflow-hidden bg-gray-100 border-2 border-gray-200 relative flex items-center justify-center shadow-md">
                                        @if(!$isDefault && $imgSrc)
                                            <img src="{{ $imgSrc }}" alt="{{ $p->product_name }}" class="w-full h-full object-cover"
                                                onerror="this.onerror=null; this.src='{{ asset('images/default-product.webp') }}';">
                                            @if($totalGalleryCount > 0)
                                                <span
                                                    class="absolute bottom-0 right-0 bg-black/75 text-white text-xs px-2 py-0.5 font-bold rounded-tl-lg">+{{ $totalGalleryCount }}</span>
                                            @endif
                                        @else
                                            <span class="text-3xl opacity-40">🖼️</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Product Info -->
                                <td class="py-3 px-5">
                                    <span
                                        class="font-black text-gray-900 text-base block leading-tight">{{ $p->product_name }}</span>
                                    <span class="text-sm text-gray-500 font-semibold">{{ $p->category }}</span>
                                    @if($items->where('is_featured', 1)->count() > 0)
                                        <span
                                            class="inline-block bg-amber-100 text-amber-900 text-xs font-bold px-2.5 py-1 rounded-lg ml-1 mt-1.5">★
                                            Featured</span>
                                    @endif
                                </td>

                                <!-- Pack Sizes & Prices -->
                                <td class="py-3 px-5">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($items as $variant)
                                            <span
                                                class="inline-flex items-center gap-2 bg-gray-100 border border-gray-200 text-gray-800 text-sm font-bold px-3 py-1.5 rounded-lg">
                                                <span>{{ $variant->size }}</span>
                                                <span
                                                    class="text-green-800 font-black">₹{{ number_format($variant->price, 0) }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </td>

                                <!-- Store Status Button -->
                                <td class="py-3 px-5 text-center">
                                    <form action="{{ route('admin.products.toggleStatus', $p->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Click to toggle shop visibility"
                                            class="text-sm font-black px-4 py-2 rounded-xl transition border cursor-pointer {{ $p->is_active ? 'bg-emerald-50 text-emerald-800 border-emerald-300 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-300' : 'bg-gray-100 text-gray-500 border-gray-300 hover:bg-emerald-50 hover:text-emerald-800' }}">
                                            {{ $p->is_active ? '🟢 Live in Shop' : '⚪ Hidden' }}
                                        </button>
                                    </form>
                                </td>

                                <!-- Edit Action -->
                                <td class="py-3 px-5 text-right">
                                    <a href="{{ route('admin.products.edit', $p->id) }}"
                                        class="bg-gray-100 hover:bg-green-800 hover:text-white text-gray-700 font-bold text-sm px-4 py-2 rounded-xl border border-gray-200 transition inline-block cursor-pointer">
                                        Edit Sizes & Media ✏️
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-gray-400 italic text-base">
                                    No products found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Bottom Section: Pagination Footer -->
            <div
                class="shrink-0 p-4 bg-gray-50 border-t-2 border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-gray-500 text-sm font-bold">
                    Showing <span class="text-gray-900 font-black"
                        x-text="totalMatchingItems > 0 ? ((currentPage - 1) * perPage) + 1 : 0"></span>
                    to <span class="text-gray-900 font-black"
                        x-text="Math.min(currentPage * perPage, totalMatchingItems)"></span>
                    of <span class="text-gray-900 font-black" x-text="totalMatchingItems"></span> products
                </div>

                <!-- Page Buttons -->
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" x-on:click="prevPage()" :disabled="currentPage === 1"
                        class="px-4 py-2 rounded-xl border font-black transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed bg-white border-gray-200 hover:bg-gray-100 text-gray-800 text-sm">
                        ‹ Prev
                    </button>

                    <template x-for="p in visiblePages" :key="p">
                        <button type="button" x-on:click="setPage(p)" :class="currentPage === p 
                                        ? 'bg-green-950 text-white font-black shadow-xs ring-1 ring-green-900' 
                                        : 'bg-white hover:bg-gray-100 text-gray-700 font-bold border border-gray-200'"
                            class="w-10 h-10 rounded-xl flex items-center justify-center transition cursor-pointer text-sm"
                            x-text="p">
                        </button>
                    </template>

                    <button type="button" x-on:click="nextPage()" :disabled="currentPage === totalPages"
                        class="px-4 py-2 rounded-xl border font-black transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed bg-white border-gray-200 hover:bg-gray-100 text-gray-800 text-sm">
                        Next ›
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Script for Tab & Page Handling -->
    <script>
        function productTableManager() {
            return {
                activeCategory: sessionStorage.getItem('admin_active_category') || '{{ !empty($category) ? $category : 'all' }}',
                currentPage: parseInt(sessionStorage.getItem('admin_current_page') || '1'),
                perPage: 5, // Adjusted for larger product display

                get matchingRows() {
                    const rows = Array.from(document.querySelectorAll('.product-row'));
                    if (this.activeCategory === 'all') {
                        return rows;
                    }
                    return rows.filter(r => r.getAttribute('data-category') === this.activeCategory);
                },

                get totalMatchingItems() {
                    return this.matchingRows.length;
                },

                get totalPages() {
                    return Math.max(1, Math.ceil(this.totalMatchingItems / this.perPage));
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
                    const catMatch = (this.activeCategory === 'all' || el.getAttribute('data-category') === this.activeCategory);
                    if (!catMatch) return false;

                    const indexInFiltered = this.matchingRows.indexOf(el);
                    const startIndex = (this.currentPage - 1) * this.perPage;
                    const endIndex = startIndex + this.perPage;

                    return indexInFiltered >= startIndex && indexInFiltered < endIndex;
                }
            };
        }
    </script>
@endsection