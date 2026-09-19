@extends('layouts.admin')

@section('admin_content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900">Quotation Orders & Fulfillment</h1>
                <p class="text-xs text-gray-500">Review WhatsApp orders, approve items, and dispatch deliveries.</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.orders') }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold {{ !request('status') ? 'bg-green-800 text-white' : 'bg-white border text-gray-700' }}">All</a>
                <a href="{{ route('admin.orders', ['status' => 'pending']) }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request('status') == 'pending' ? 'bg-amber-500 text-white' : 'bg-white border text-gray-700' }}">Pending</a>
                <a href="{{ route('admin.orders', ['status' => 'approved']) }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request('status') == 'approved' ? 'bg-green-700 text-white' : 'bg-white border text-gray-700' }}">Approved</a>
                <a href="{{ route('admin.orders', ['status' => 'dispatched']) }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request('status') == 'dispatched' ? 'bg-blue-600 text-white' : 'bg-white border text-gray-700' }}">Dispatched</a>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($orders as $o)
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-2xs space-y-4">
                    <div class="flex flex-wrap justify-between items-center gap-2 pb-3 border-b border-gray-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-gray-900 text-base">#{{ $o->order_number }}</span>
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $o->status === 'approved' ? 'bg-green-100 text-green-800' : ($o->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($o->status === 'dispatched' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                    ● {{ $o->status }}
                                </span>
                            </div>
                            <div class="text-xs font-bold text-gray-700 mt-1">
                                {{ $o->customer_name }} • <span
                                    class="text-gray-400 font-medium">{{ $o->customer_phone }}</span>
                            </div>
                            @if($o->delivery_address)
                                <div class="text-[11px] text-gray-500 mt-0.5 font-normal">📍 {{ $o->delivery_address }}</div>
                            @endif
                        </div>

                        <div class="text-right">
                            <span class="text-[10px] text-gray-400 font-bold uppercase block">Quotation Value</span>
                            <span class="text-lg font-black text-green-900">₹{{ number_format($o->total_amount, 2) }}</span>
                        </div>
                    </div>

                    <!-- Line Items -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 text-xs">
                        @foreach($o->items as $item)
                            <div class="bg-gray-50 p-2.5 rounded-xl flex justify-between border border-gray-100">
                                <div>
                                    <span class="font-bold text-gray-900 block">{{ $item->product_name }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $item->size }}</span>
                                </div>
                                <span class="font-bold text-gray-700">{{ $item->quantity }} ×
                                    ₹{{ number_format($item->unit_price, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Status Update Form -->
                    <form method="POST" action="{{ route('admin.orders.status', $o->id) }}"
                        class="pt-2 flex flex-wrap items-center justify-between gap-3 border-t border-gray-50">
                        @csrf
                        @method('PATCH')

                        <div class="flex items-center gap-2">
                            <select name="status"
                                class="bg-gray-50 border border-gray-200 text-xs rounded-xl p-2 font-bold text-gray-800 outline-none">
                                <option value="pending" {{ $o->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $o->status === 'approved' ? 'selected' : '' }}>Approve / Accept
                                </option>
                                <option value="dispatched" {{ $o->status === 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                                <option value="delivered" {{ $o->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="rejected" {{ $o->status === 'rejected' ? 'selected' : '' }}>Reject Quote</option>
                            </select>
                            <button type="submit"
                                class="bg-green-800 hover:bg-green-700 text-white font-bold text-xs px-4 py-2 rounded-xl transition">Update
                                Status</button>
                        </div>

                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $o->customer_phone) }}?text=Hello%20{{ urlencode($o->customer_name) }},%20your%20PatSons%20Goa%20Order%20%23{{ $o->order_number }}%20status%20is%20now:%20{{ strtoupper($o->status) }}."
                            target="_blank"
                            class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-4 py-2 rounded-xl flex items-center gap-1.5 shadow-2xs transition">
                            <span>💬 Message Customer on WhatsApp</span>
                        </a>
                    </form>
                </div>
            @empty
                <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 text-gray-400 text-xs">
                    No orders found for this filter.
                </div>
            @endforelse

            {{ $orders->links() }}
        </div>
    </div>
@endsection