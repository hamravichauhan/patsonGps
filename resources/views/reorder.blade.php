@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto px-4 py-10">
        <div class="bg-white p-6 rounded-2xl border shadow-sm">
            <h2 class="text-xl font-bold mb-2">Quick Reorder</h2>
            <p class="text-xs text-gray-500 mb-4">Enter your phone number to reorder your previous items.</p>

            <form method="GET" action="{{ route('order.reorder') }}" class="flex gap-2 mb-6">
                <input type="tel" name="phone" value="{{ request('phone') }}" placeholder="Enter 10-digit number"
                    class="p-2.5 border rounded-xl text-sm flex-1 outline-none focus:ring-2 focus:ring-green-500" required>
                <button type="submit"
                    class="bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold">Search</button>
            </form>

            @if(request('phone'))
                <div class="space-y-4">
                    @forelse($orders as $order)
                        @php $items = json_decode($order->order_items, true); @endphp
                        <div class="border rounded-xl p-4 flex justify-between items-center bg-gray-50">
                            <div>
                                <p class="text-xs font-bold text-gray-500">{{ $order->order_number }} &bull;
                                    {{ date('d M Y', strtotime($order->created_at)) }}</p>
                                <p class="text-sm font-medium mt-1">
                                    @foreach($items as $i) {{ $i['name'] }} (x{{ $i['quantity'] }})@if(!$loop->last), @endif
                                    @endforeach
                                </p>
                                <p class="text-xs font-bold text-green-700 mt-1">Total:
                                    ₹{{ number_format($order->total_amount, 2) }}</p>
                            </div>
                            <button @click='cart = {{ json_encode($items) }}; saveCart(); cartOpen = true;'
                                class="bg-green-600 text-white text-xs font-bold px-3 py-2 rounded-lg">
                                Reorder
                            </button>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No past orders found for this number.</p>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
@endsection