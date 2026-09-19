@extends('layouts.admin')

@section('admin_content')
    <div class="max-w-2xl bg-white rounded-3xl p-8 border border-gray-100 shadow-2xs space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-xl font-black text-gray-900">Add New Product Variant</h1>
                <p class="text-xs text-gray-500">Add a new SKU to the PatSons Goan product catalog.</p>
            </div>
            <a href="{{ route('admin.products') }}" class="text-xs font-bold text-gray-500 hover:text-gray-900">← Back</a>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data"
            class="space-y-4 text-xs font-bold">
            @csrf

            <div>
                <label class="block uppercase text-gray-400 mb-1">Product Name</label>
                <input type="text" name="product_name" required placeholder="e.g. Kokum Agal (Pure Extract)"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-gray-800 outline-none focus:ring-2 focus:ring-green-800">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block uppercase text-gray-400 mb-1">Category</label>
                    <input type="text" name="category" list="cat-list" required placeholder="Kokum & Agal"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-gray-800 outline-none focus:ring-2 focus:ring-green-800">
                    <datalist id="cat-list">
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}"> @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="block uppercase text-gray-400 mb-1">Pack Size</label>
                    <input type="text" name="size" required placeholder="e.g. 500ml Bottle, 5 Litre Can"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-gray-800 outline-none focus:ring-2 focus:ring-green-800">
                </div>
            </div>

            <div>
                <label class="block uppercase text-gray-400 mb-1">Price (₹)</label>
                <input type="number" step="0.01" name="price" required placeholder="120.00"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-gray-800 outline-none focus:ring-2 focus:ring-green-800">
            </div>

            <div>
                <label class="block uppercase text-gray-400 mb-1">Description / Recipe Details</label>
                <textarea name="description" rows="3" placeholder="Handcrafted from pure coastal Goan fruits..."
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-gray-800 outline-none focus:ring-2 focus:ring-green-800"></textarea>
            </div>

            <div>
                <label class="block uppercase text-gray-400 mb-1">Product Photo</label>
                <input type="file" name="image_file" accept="image/*" class="w-full text-xs text-gray-500">
            </div>

            <div class="flex items-center gap-6 pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="rounded text-green-800">
                    <span>Featured / Best Seller</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-green-800">
                    <span>Active in Store</span>
                </label>
            </div>

            <button type="submit"
                class="w-full bg-green-800 hover:bg-green-700 text-white font-black py-3.5 rounded-xl shadow transition mt-4">
                Save Product to Catalog →
            </button>
        </form>
    </div>
@endsection