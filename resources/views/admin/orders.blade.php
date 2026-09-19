@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Customer Quotations & Orders</h1>

        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100 text-gray-600 border-b">
                    <tr>
                        <th class="p-3">Order #</th>
                        <th class="p-3">Customer</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">Amount</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($orders as $o)
                        <tr>
                            <td class="p-3 font-mono font-bold">{{ $o->order_number }}</td>
                            <td class="p-3">
                                <p class="font-semibold">{{ $o->customer_name }}</p>
                                <p class="text-xs text-gray-500">{{ $o->customer_phone }}</p>
                            </td>
                            <td class="p-3"><span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $o->order_type }}</span></td>
                            <td class="p-3 font-bold text-green-700">₹{{ number_format($o->total_amount, 2) }}</td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 text-xs rounded-full font-bold
                                        {{ $o->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $o->status === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $o->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($o->status) }}
                                </span>
                            </td>
                            <td class="p-3">
                                <form action="{{ route('admin.orders.status', $o->id) }}" method="POST" class="flex gap-1">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="text-xs border rounded p-1">
                                        <option value="pending" {{ $o->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $o->status == 'approved' ? 'selected' : '' }}>Approved
                                        </option>
                                        <option value="completed" {{ $o->status == 'completed' ? 'selected' : '' }}>Completed
                                        </option>
                                        <option value="cancelled" {{ $o->status == 'cancelled' ? 'selected' : '' }}>Cancelled
                                        </option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-400">No orders placed yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection