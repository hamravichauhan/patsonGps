@extends('layouts.app')

@section('title', 'Track Order & Order History - PatSons Foods Goa')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12" x-data="trackingAuth()">

        @if(!$phone)
            <!-- ==================== STATE 1: OTP LOGIN / VERIFICATION ==================== -->
            <div
                class="max-w-md mx-auto bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-xl text-center space-y-6">
                <div
                    class="w-14 h-14 bg-green-50 text-green-900 border border-green-200 rounded-2xl flex items-center justify-center mx-auto text-2xl">
                    📦
                </div>

                <div class="space-y-1">
                    <h1 class="text-2xl font-black text-stone-900 tracking-tight">Track Your Order</h1>
                    <p class="text-xs text-stone-500 font-medium">Enter your WhatsApp number to view real-time status and past
                        quotations.</p>
                </div>

                <!-- STEP 1: Phone Entry -->
                <div x-show="step === 'phone'" class="space-y-4 text-left">
                    <div>
                        <label class="block text-[11px] font-black text-stone-700 uppercase tracking-wider mb-1">WhatsApp
                            Number</label>
                        <div class="flex">
                            <span
                                class="inline-flex items-center px-3.5 rounded-l-xl border border-r-0 border-stone-300 bg-stone-100 text-stone-600 font-bold text-xs">
                                +91
                            </span>
                            <input type="tel" x-model="phone" maxlength="10" placeholder="10-digit mobile number"
                                class="w-full bg-white border border-stone-300 rounded-r-xl px-3.5 py-2.5 text-xs font-bold outline-none focus:ring-2 focus:ring-green-800">
                        </div>
                    </div>

                    <button type="button" @click="sendOtp()" :disabled="isLoading"
                        class="w-full bg-green-900 hover:bg-green-800 active:scale-98 text-white font-black text-xs py-3.5 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                        <span x-show="!isLoading">📲 Send WhatsApp OTP</span>
                        <span x-show="isLoading" x-cloak>Sending Code...</span>
                    </button>
                </div>

                <!-- STEP 2: 4-Digit OTP Entry -->
                <div x-show="step === 'otp'" x-cloak class="space-y-4 text-left">
                    <div class="bg-amber-50 border border-amber-200/80 p-3 rounded-xl text-center">
                        <span class="text-xs text-amber-950 font-bold">4-digit code sent to +91 <strong
                                x-text="phone"></strong></span>
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-black text-stone-700 uppercase tracking-wider mb-1 text-center">Enter
                            4-Digit OTP</label>
                        <input type="text" maxlength="4" x-model="otp" placeholder="• • • •"
                            class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-center text-xl font-black tracking-widest outline-none focus:ring-2 focus:ring-green-800">
                    </div>

                    <button type="button" @click="verifyOtp()" :disabled="isLoading"
                        class="w-full bg-amber-400 hover:bg-amber-300 active:scale-98 text-stone-950 font-black text-xs py-3.5 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                        <span x-show="!isLoading">✓ Verify & View Orders</span>
                        <span x-show="isLoading" x-cloak>Verifying...</span>
                    </button>

                    <div class="text-center pt-1">
                        <button type="button" @click="step = 'phone'; otp = ''"
                            class="text-xs font-bold text-stone-500 hover:text-green-900 underline">
                            ← Change Mobile Number
                        </button>
                    </div>
                </div>

                <div class="pt-2 border-t border-stone-100 text-stone-400 text-[10px] leading-relaxed">
                    Protected with instant WhatsApp OTP verification. No password required.
                </div>
            </div>

        @else
            <!-- ==================== STATE 2: ACTIVE ORDER TRACKING DASHBOARD ==================== -->
            <div class="space-y-6">

                <!-- Top Header & Session Bar -->
                <div
                    class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <span
                            class="text-[10px] font-black text-green-900 bg-green-50 px-3 py-1 rounded-full uppercase tracking-wider">
                            Authenticated Customer
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-black text-stone-900 mt-2">
                            +91 {{ substr($phone, 0, 5) }} {{ substr($phone, 5) }}
                        </h1>
                        <p class="text-xs text-stone-500 font-medium mt-0.5">Tracking {{ $orders->count() }} past orders /
                            quotation requests</p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('shop') }}"
                            class="bg-amber-400 hover:bg-amber-300 text-stone-950 font-black text-xs px-4 py-2.5 rounded-xl shadow-xs transition">
                            + New Order
                        </a>
                        <form action="{{ route('customer.logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs px-4 py-2.5 rounded-xl transition cursor-pointer">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Orders List -->
                @if($orders->isEmpty())
                    <div class="bg-white rounded-3xl p-12 text-center border border-stone-200 space-y-3">
                        <span class="text-4xl block">🍃</span>
                        <h3 class="text-base font-black text-stone-900">No Orders Found</h3>
                        <p class="text-xs text-stone-500 max-w-sm mx-auto">No orders have been submitted yet under this phone
                            number.</p>
                        <a href="{{ route('shop') }}"
                            class="inline-block bg-green-900 text-white font-black text-xs px-5 py-2.5 rounded-xl mt-2">
                            Explore Catalog & Order →
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($orders as $order)
                            <div
                                class="bg-white rounded-3xl p-5 sm:p-7 border border-stone-200 shadow-xs hover:border-green-300 transition-all space-y-4">

                                <!-- Order Header -->
                                <div
                                    class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-stone-100 gap-2">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-black font-mono bg-stone-100 px-3 py-1 rounded-lg text-stone-800">
                                            #{{ $order->order_number }}
                                        </span>
                                        <span class="text-xs text-stone-400 font-semibold">
                                            {{ $order->created_at->format('d M Y, h:i A') }}
                                        </span>
                                    </div>

                                    <!-- Status Badges -->
                                    <div>
                                        @if($order->status === 'approved' || $order->status === 'completed')
                                            <span class="bg-green-100 text-green-900 text-xs font-black px-3 py-1 rounded-full">
                                                ✓ {{ ucfirst($order->status) }}
                                            </span>
                                        @elseif($order->status === 'pending')
                                            <span class="bg-amber-100 text-amber-950 text-xs font-black px-3 py-1 rounded-full">
                                                ⏳ Processing / Review
                                            </span>
                                        @else
                                            <span class="bg-stone-100 text-stone-700 text-xs font-black px-3 py-1 rounded-full">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Items Breakdown -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                    @php
                                        $items = json_decode($order->order_items, true) ?: [];
                                    @endphp
                                    @foreach($items as $item)
                                        <div
                                            class="p-3 rounded-xl bg-stone-50 border border-stone-100 flex items-center justify-between text-xs">
                                            <div>
                                                <span class="font-bold text-stone-900 block">{{ $item['name'] ?? 'Product' }}</span>
                                                <span class="text-[10px] text-stone-500 font-semibold">{{ $item['size'] ?? 'Standard' }} ×
                                                    {{ $item['quantity'] ?? 1 }}</span>
                                            </div>
                                            <span
                                                class="font-black text-stone-900">₹{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Order Footer & Reorder Action -->
                                <div
                                    class="pt-3 border-t border-stone-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                    <div>
                                        <span class="text-[10px] text-stone-400 font-bold uppercase block">Total Quotation Value</span>
                                        <span
                                            class="text-lg font-black text-green-950">₹{{ number_format($order->total_amount, 2) }}</span>
                                    </div>

                                    <div class="flex items-center gap-2 w-full sm:w-auto">
                                        <button type="button" @click="reorder('{{ $order->order_number }}')"
                                            class="w-full sm:w-auto bg-green-900 hover:bg-green-800 active:scale-95 text-white font-black text-xs px-4 py-2.5 rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                                            <span>🔄 Reorder Items</span>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        @endif

    </div>

    <script>
        function trackingAuth() {
            return {
                step: 'phone',
                phone: '',
                otp: '',
                isLoading: false,

                async sendOtp() {
                    if (!this.phone || this.phone.trim().length < 10) {
                        alert('Please enter a valid 10-digit WhatsApp number.');
                        return;
                    }

                    this.isLoading = true;
                    try {
                        let res = await fetch('{{ route('customer.otp.send') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ phone: this.phone })
                        });
                        let data = await res.json();
                        if (data.status === 'success') {
                            this.step = 'otp';
                        } else {
                            alert(data.message || 'Error sending OTP.');
                        }
                    } catch (e) {
                        alert('Request error: ' + e.message);
                    } finally {
                        this.isLoading = false;
                    }
                },

                async verifyOtp() {
                    if (!this.otp || this.otp.trim().length !== 4) {
                        alert('Please enter the 4-digit code.');
                        return;
                    }

                    this.isLoading = true;
                    try {
                        let res = await fetch('{{ route('customer.otp.verify') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ phone: this.phone, otp: this.otp })
                        });
                        let data = await res.json();
                        if (data.status === 'success') {
                            window.location.reload();
                        } else {
                            alert(data.message || 'Invalid OTP code.');
                        }
                    } catch (e) {
                        alert('Verification error: ' + e.message);
                    } finally {
                        this.isLoading = false;
                    }
                },

                async reorder(orderNumber) {
                    try {
                        let res = await fetch('/api/reorder/' + orderNumber);
                        let data = await res.json();
                        if (data.status === 'success' && Array.isArray(data.items)) {
                            localStorage.setItem('patsons_cart', JSON.stringify(data.items));
                            window.location.reload();
                        } else {
                            alert(data.message || 'Could not load reorder items.');
                        }
                    } catch (e) {
                        alert('Error loading reorder: ' + e.message);
                    }
                }
            }
        }
    </script>
@endsection