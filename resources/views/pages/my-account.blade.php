@extends('layouts.app')

@section('title', 'Track Order & Quotation History - PatSons Foods Goa')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12" x-data="customerAccountManager()">

        @if(!$phone)
            <!-- ==================== STATE 1: SECURE WHATSAPP OTP LOGIN ==================== -->
            <div
                class="max-w-md mx-auto bg-white rounded-3xl p-6 sm:p-10 border border-stone-200 shadow-xl text-center space-y-6">
                <div
                    class="w-16 h-16 bg-green-50 text-green-900 border border-green-200/80 rounded-2xl flex items-center justify-center text-3xl mx-auto shadow-xs">
                    📦
                </div>

                <div class="space-y-1">
                    <h1 class="text-2xl font-black text-stone-900 tracking-tight">Track Your Orders</h1>
                    <p class="text-xs text-stone-500 font-medium leading-relaxed">
                        Verify your WhatsApp number to view real-time quotation status and 1-click reorders.
                    </p>
                </div>

                <!-- Step 1: Mobile Number Entry -->
                <div x-show="step === 'phone'" class="space-y-4 text-left">
                    <div>
                        <label class="block text-[11px] font-black text-stone-700 uppercase tracking-wider mb-1">
                            WhatsApp Number *
                        </label>
                        <div class="flex">
                            <span
                                class="inline-flex items-center px-3.5 rounded-l-xl border border-r-0 border-stone-300 bg-stone-100 text-stone-600 font-bold text-xs">
                                +91
                            </span>
                            <input type="tel" x-model="phone" maxlength="10" placeholder="10-digit mobile number"
                                class="w-full bg-white border border-stone-300 rounded-r-xl px-3.5 py-2.5 text-xs font-bold text-stone-900 outline-none focus:ring-2 focus:ring-green-800">
                        </div>
                    </div>

                    <button type="button" @click="sendOtp()" :disabled="isLoading"
                        class="w-full bg-green-900 hover:bg-green-800 active:scale-98 text-white font-black text-xs py-3.5 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                        <span x-show="!isLoading">📲 Send 4-Digit Verification Code</span>
                        <span x-show="isLoading" x-cloak>Sending WhatsApp OTP...</span>
                    </button>
                </div>

                <!-- Step 2: 4-Digit OTP Entry -->
                <div x-show="step === 'otp'" x-cloak class="space-y-4 text-left">
                    <div class="bg-emerald-50 border border-emerald-200 p-3 rounded-xl text-center">
                        <span class="text-xs text-emerald-950 font-bold">
                            Code sent to WhatsApp: <strong>+91 <span x-text="phone"></span></strong>
                        </span>
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-stone-700 uppercase tracking-wider mb-1 text-center">
                            Enter 4-Digit Code
                        </label>
                        <input type="text" maxlength="4" x-model="otp" placeholder="• • • •"
                            class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-center text-xl font-black tracking-widest text-stone-900 outline-none focus:ring-2 focus:ring-green-800">
                    </div>

                    <button type="button" @click="verifyOtp()" :disabled="isLoading"
                        class="w-full bg-amber-400 hover:bg-amber-300 active:scale-98 text-stone-950 font-black text-xs py-3.5 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                        <span x-show="!isLoading">✓ Verify & View Order History</span>
                        <span x-show="isLoading" x-cloak>Verifying...</span>
                    </button>

                    <div class="text-center pt-1">
                        <button type="button" @click="step = 'phone'; otp = ''"
                            class="text-xs font-bold text-stone-500 hover:text-green-900 underline">
                            ← Change Phone Number
                        </button>
                    </div>
                </div>

                <div class="pt-3 border-t border-stone-100 text-[11px] text-stone-400">
                    Dev Can Fruit Products • Morjim, Pernem - Goa
                </div>
            </div>

        @else
            <!-- ==================== STATE 2: VERIFIED ORDER HISTORY DASHBOARD ==================== -->
            <div class="space-y-6">

                <!-- Customer Account Bar -->
                <div
                    class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <span
                            class="text-[10px] font-black uppercase tracking-wider text-green-900 bg-green-50 px-3 py-1 rounded-full">
                            Verified Session
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-black text-stone-900 mt-2">
                            {{ $customerName ?? 'Valued Customer' }}
                        </h1>
                        <p class="text-xs font-mono text-stone-500 mt-0.5 font-bold">+91 {{ $phone }}</p>
                    </div>

                    <div class="flex items-center gap-3">
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

                <!-- Order Tracking List -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-black text-stone-800 uppercase tracking-wider">
                            Past Orders & Quotations ({{ $orders->count() }})
                        </h2>
                    </div>

                    @if($orders->isEmpty())
                        <div class="bg-white rounded-3xl p-12 text-center border border-stone-200 space-y-3">
                            <span class="text-4xl block">🍃</span>
                            <h3 class="text-base font-black text-stone-900">No Orders Found</h3>
                            <p class="text-xs text-stone-500 max-w-sm mx-auto">No orders have been submitted yet under +91
                                {{ $phone }}.</p>
                            <a href="{{ route('shop') }}"
                                class="inline-block bg-green-900 text-white font-black text-xs px-5 py-2.5 rounded-xl mt-2 shadow-xs hover:bg-green-800 transition">
                                Browse Store Catalog →
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($orders as $o)
                                <div
                                    class="bg-white rounded-3xl p-5 sm:p-7 border border-stone-200 shadow-xs hover:border-green-300 transition-all space-y-4">

                                    <!-- Header Row -->
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-stone-100 gap-2">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-black font-mono bg-stone-100 px-3 py-1 rounded-lg text-stone-800">
                                                #{{ $o->order_number }}
                                            </span>
                                            <span class="text-xs text-stone-400 font-semibold">
                                                {{ $o->created_at->format('d M Y • h:i A') }}
                                            </span>
                                        </div>

                                        <div>
                                            <span class="text-xs font-black uppercase px-3 py-1 rounded-full 
                                                            @if($o->status === 'approved' || $o->status === 'completed') bg-green-100 text-green-800 
                                                            @elseif($o->status === 'dispatched') bg-blue-100 text-blue-800 
                                                            @elseif($o->status === 'rejected') bg-red-100 text-red-800 
                                                            @else bg-amber-100 text-amber-800 @endif">
                                                {{ $o->status === 'pending' ? '⏳ Under Review' : '✓ ' . ucfirst($o->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Relational / Decoded Items -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                        @php
                                            $items = json_decode($o->order_items, true) ?: [];
                                        @endphp
                                        @foreach($items as $item)
                                            <div
                                                class="p-3 rounded-2xl bg-stone-50 border border-stone-100 flex items-center justify-between text-xs">
                                                <div class="space-y-0.5">
                                                    <span class="font-bold text-stone-900 block">{{ $item['name'] ?? 'Product' }}</span>
                                                    <span class="text-[10px] text-stone-500 font-semibold">{{ $item['size'] ?? 'Standard' }}
                                                        × {{ $item['quantity'] ?? 1 }}</span>
                                                </div>
                                                <span
                                                    class="font-black text-stone-900">₹{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Footer Row -->
                                    <div
                                        class="pt-3 border-t border-stone-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                        <div>
                                            <span class="text-[10px] text-stone-400 font-bold uppercase block">Total Quotation
                                                Amount</span>
                                            <span
                                                class="text-lg font-black text-green-950">₹{{ number_format($o->total_amount, 2) }}</span>
                                        </div>

                                        <div class="flex items-center gap-2 w-full sm:w-auto">
                                            <a href="https://wa.me/917758943614?text=Hello%20PatSons!%20Inquiring%20about%20Order%20%23{{ $o->order_number }}."
                                                target="_blank"
                                                class="w-full sm:w-auto text-xs font-bold text-green-900 hover:bg-green-50 border border-green-200 px-3.5 py-2.5 rounded-xl transition text-center">
                                                💬 Inquiry on WhatsApp
                                            </a>

                                            <button type="button" @click="reorder('{{ $o->order_number }}')" :disabled="loadingReorder"
                                                class="w-full sm:w-auto bg-green-900 hover:bg-green-800 active:scale-95 text-white text-xs font-black px-4 py-2.5 rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                                                <span>🔁 1-Click Reorder</span>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        @endif

    </div>

    <!-- Customer OTP & Reorder Logic -->
    <script>
        function customerAccountManager() {
            return {
                step: 'phone',
                phone: '',
                otp: '',
                isLoading: false,
                loadingReorder: false,

                async sendOtp() {
                    if (!this.phone || this.phone.trim().length < 10) {
                        alert('Please enter a valid 10-digit WhatsApp phone number.');
                        return;
                    }

                    this.isLoading = true;
                    try {
                        let res = await fetch('{{ route('customer.otp.send') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ phone: this.phone })
                        });
                        let data = await res.json();
                        if (data.status === 'success') {
                            this.step = 'otp';
                        } else {
                            alert(data.message || 'Failed to send OTP.');
                        }
                    } catch (e) {
                        alert('Error sending OTP: ' + e.message);
                    } finally {
                        this.isLoading = false;
                    }
                },

                async verifyOtp() {
                    if (!this.otp || this.otp.trim().length !== 4) {
                        alert('Please enter the 4-digit code sent to your WhatsApp.');
                        return;
                    }

                    this.isLoading = true;
                    try {
                        let res = await fetch('{{ route('customer.otp.verify') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ phone: this.phone, otp: this.otp })
                        });
                        let data = await res.json();
                        if (data.status === 'success') {
                            window.location.reload();
                        } else {
                            alert(data.message || 'Invalid verification code.');
                        }
                    } catch (e) {
                        alert('Verification error: ' + e.message);
                    } finally {
                        this.isLoading = false;
                    }
                },

                async reorder(orderNumber) {
                    this.loadingReorder = true;
                    try {
                        let res = await fetch(`/api/reorder/${orderNumber}`);
                        let data = await res.json();
                        if (data.status === 'success' && data.cart_items && data.cart_items.length > 0) {
                            localStorage.setItem('patsons_cart', JSON.stringify(data.cart_items));
                            window.location.href = '{{ route('shop') }}';
                        } else {
                            alert(data.message || 'Could not retrieve items from this order.');
                        }
                    } catch (e) {
                        alert('Reorder error: ' + e.message);
                    } finally {
                        this.loadingReorder = false;
                    }
                }
            }
        }
    </script>
@endsection