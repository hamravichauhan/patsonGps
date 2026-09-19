@extends('layouts.admin')

@section('admin_content')
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900">Product Catalog Management</h1>
                <p class="text-xs text-gray-500 mt-0.5">Manage bottle variants, pricing, descriptions, and stock statuses.
                </p>
            </div>
            <a href="{{ route('admin.products.create') }}"
                class="bg-green-800 hover:bg-green-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs transition flex items-center gap-1.5">
                <span>+</span> <span>Add New Product SKU</span>
            </a>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 font-bold uppercase text-[10px]">
                            <th class="p-4">Product / Variant</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Pack Size</th>
                            <th class="p-4">Price</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium">
                        @foreach($products as $p)
                            <tr class="hover:bg-gray-50/50">
                                <td class="p-4 flex items-center gap-3">
                                    <img src="/images/products/{{ $p->image ?? 'default-product.webp' }}" alt=""
                                        class="w-10 h-10 object-contain rounded-lg border border-gray-100 bg-gray-50 p-1">
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $p->product_name }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $p->slug }}</div>
                                    </div>
                                </td>
                                <td class="p-4 text-gray-600">{{ $p->category }}</td>
                                <td class="p-4 font-bold text-gray-700">{{ $p->size }}</td>
                                <td class="p-4 font-black text-green-900">₹{{ number_format($p->price, 2) }}</td>
                                <td class="p-4">
                                    <span
                                        class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $p->is_active ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}">
                                        {{ $p->is_active ? 'Active' : 'Disabled' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-2">
                                    <a href="{{ route('admin.products.edit', $p->id) }}"
                                        class="text-indigo-600 hover:underline font-bold">Edit</a>
                                    <form method="POST" action="{{ route('admin.products.delete', $p->id) }}"
                                        class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to delete this product SKU?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline font-bold">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $products->links() }}
            </div>
        </div>

    </div>
@endsection