@extends('layouts.admin')

@section('admin_content')
    <div class="space-y-8">

        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Executive Dashboard</h1>
            <p class="text-xs text-gray-500 mt-1">Real-time store metrics, pending quotes, and inventory status.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs">
                <span class="text-[10px] font-bold uppercase text-gray-400">Total Orders</span>
                <div class="text-2xl font-black text-gray-900 mt-1">{{ $stats['total_orders'] }}</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs">
                <span class="text-[10px] font-bold uppercase text-amber-600">Pending Actions</span>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['pending_orders'] }}</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs">
                <span class="text-[10px] font-bold uppercase text-green-700">Revenue Approximated</span>
                <div class="text-2xl font-black text-green-800 mt-1">₹{{ number_format($stats['total_revenue'], 2) }}</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs">
                <span class="text-[10px] font-bold uppercase text-gray-400">Catalog SKUs</span>
                <div class="text-2xl font-black text-gray-900 mt-1">{{ $stats['total_products'] }}</div>
            </div>
        </div>

        <!-- Recent Orders Section -->
        <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-2xs space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-black text-gray-900">Latest WhatsApp Quotations</h2>
                <a href="{{ route('admin.orders') }}" class="text-xs text-green-800 font-bold hover:underline">View All
                    Orders →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 font-bold uppercase text-[10px]">
                            <th class="pb-3">Order #</th>
                            <th class="pb-3">Customer</th>
                            <th class="pb-3">Items</th>
                            <th class="pb-3">Total</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3 text-right">Quick Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 font-medium">
                        @forelse($recentOrders as $o)
                            <tr>
                                <td class="py-3.5 font-bold text-gray-900">#{{ $o->order_number }}</td>
                                <td class="py-3.5">
                                    <div class="font-bold text-gray-900">{{ $o->customer_name }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $o->customer_phone }}</div>
                                </td>
                                <td class="py-3.5 text-gray-600">{{ $o->items->count() }} items</td>
                                <td class="py-3.5 font-bold text-green-900">₹{{ number_format($o->total_amount, 2) }}</td>
                                <td class="py-3.5">
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $o->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($o->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700') }}">
                                        {{ $o->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 text-right">
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $o->customer_phone) }}?text=Hello%20{{ urlencode($o->customer_name) }},%20this%20is%20PatSons%20Foods%20Goa%20regarding%20Order%20%23{{ $o->order_number }}."
                                        target="_blank"
                                        class="inline-block bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-1 rounded-xl text-[10px] font-bold">
                                        💬 WhatsApp
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">No recent orders yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection