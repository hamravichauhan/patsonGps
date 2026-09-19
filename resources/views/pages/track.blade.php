@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto px-4 py-12">
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-100 shadow-sm">
            <div class="text-center mb-6">
                <span class="text-3xl">🔎</span>
                <h1 class="text-xl font-black text-gray-900 mt-2">Track Quotation / Order</h1>
                <p class="text-xs text-gray-500">Enter your Order # and 10-digit WhatsApp number to verify status.</p>
            </div>

            <form method="POST" action="{{ route('order.track.search') }}" class="space-y-3 mb-6">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Order Number (e.g. PS-XXXXX)</label>
                    <input type="text" name="order_number" value="{{ request('order_number') }}" placeholder="PS-12345"
                        class="w-full border border-gray-200 rounded-xl p-2.5 text-xs outline-none focus:ring-2 focus:ring-green-800"
                        required>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Your WhatsApp Number</label>
                    <input type="tel" name="phone" value="{{ request('phone') }}" placeholder="9822123456"
                        class="w-full border border-gray-200 rounded-xl p-2.5 text-xs outline-none focus:ring-2 focus:ring-green-800"
                        required>
                </div>
                <button type="submit"
                    class="w-full bg-green-800 hover:bg-green-900 text-white font-bold py-3 rounded-xl text-xs transition">
                    Find My Order
                </button>
            </form>

            @if(isset($order) && $order)
                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 space-y-3">
                    <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase">Order Reference</span>
                            <p class="font-extrabold text-sm text-gray-900">{{ $order->order_number }}</p>
                        </div>
                        <span class="px-2.5 py-1 text-[11px] rounded-full font-extrabold uppercase
                                {{ $order->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $order->status === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                {{ $order->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                            Status: {{ $order->status }}
                        </span>
                    </div>

                    <div class="text-xs space-y-1">
                        <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
                        <p><strong>Total Amount:</strong> ₹{{ number_format($order->total_amount, 2) }}</p>
                        <p><strong>Placed On:</strong> {{ date('d M Y, h:i A', strtotime($order->created_at)) }}</p>
                    </div>

                    <div class="pt-2 border-t border-gray-200">
                        <a href="{{ route('order.reorder.token', $order->reorder_token) }}"
                            class="block text-center bg-white border border-green-800 text-green-800 font-bold py-2 rounded-xl text-xs hover:bg-green-50 transition">
                            🔄 1-Click Reorder This Order
                        </a>
                    </div>
                </div>
            @elseif(request()->isMethod('post'))
                <p class="text-center text-xs text-red-500 py-3 font-semibold">No order found matching those details.</p>
            @endif
        </div>
    </div>
@endsection