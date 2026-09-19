@extends('layouts.admin')

@section('title', 'Store Settings & Security - PatSons Admin')

@section('content')
    <div class="max-w-4xl space-y-6 pb-12"
        x-data="{ showPasswordFields: {{ $errors->has('current_password') || $errors->has('new_password') ? 'true' : 'false' }} }">

        <!-- Header -->
        <div class="border-b border-gray-200 pb-4">
            <h1 class="text-2xl font-black text-gray-900">Store Settings & Admin Security</h1>
            <p class="text-xs text-gray-500 mt-1">
                Manage your admin login credentials, password, WhatsApp gateway device pairing, and automated order
                controls.
            </p>
        </div>

        @if(session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold p-3.5 rounded-2xl flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-700 font-bold">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-xs font-bold p-4 rounded-2xl space-y-1">
                @foreach($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- CARD 1: Self-Hosted WhatsApp Device Link & QR Status -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200 shadow-xs space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="text-lg">📲</span>
                    <div>
                        <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider">WhatsApp Business Gateway
                            Device</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Link any WhatsApp number to send automated OTPs and alerts
                            without recurring fees.</p>
                    </div>
                </div>
                <button type="button" onclick="loadWhatsAppQR()"
                    class="bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition cursor-pointer flex items-center gap-2 shadow-xs">
                    <span>⚡ Link Device / View QR</span>
                </button>
            </div>

            <!-- Interactive QR Display Area -->
            <div id="qrModal"
                class="hidden p-6 border border-dashed border-emerald-300 rounded-2xl bg-emerald-50/40 text-center space-y-3 transition">
                <div id="qrStatusBadge"
                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-emerald-200 rounded-full text-[11px] font-bold text-emerald-800 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Ready for Scanning</span>
                </div>
                <p class="text-xs text-stone-600 max-w-md mx-auto">
                    Open WhatsApp on your phone $\rightarrow$ <strong>Settings / Three Dots</strong> $\rightarrow$
                    <strong>Linked Devices</strong> $\rightarrow$ <strong>Link a Device</strong>, then point your camera at
                    this QR code.
                </p>
                <div id="qrImageContainer" class="flex justify-center my-3 min-h-[200px] items-center">
                    <span class="text-xs text-stone-500">Connecting to gateway...</span>
                </div>
                <button type="button" onclick="loadWhatsAppQR()" class="text-xs font-bold text-emerald-700 hover:underline">
                    ↻ Refresh QR Code
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf

            <!-- CARD 2: Admin Profile & Login Credentials -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200 shadow-xs space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🔐</span>
                        <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider">Admin Login & Credentials</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1">
                            Admin Name *
                        </label>
                        <input type="text" name="name" value="{{ old('name', $adminUser->name ?? '') }}" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-bold text-gray-900 outline-none focus:ring-2 focus:ring-green-800">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1">
                            Admin Email (Login ID) *
                        </label>
                        <input type="email" name="email" value="{{ old('email', $adminUser->email ?? '') }}" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-bold text-gray-900 outline-none focus:ring-2 focus:ring-green-800">
                    </div>
                </div>

                <!-- Password Action Section -->
                <div class="pt-4 border-t border-gray-100">
                    <!-- Button to toggle password fields -->
                    <div class="flex items-center justify-between" x-show="!showPasswordFields">
                        <div>
                            <span class="text-xs font-black text-gray-800 block">Password Security</span>
                            <span class="text-[11px] text-gray-400">Keep your existing password or set a new one.</span>
                        </div>
                        <button type="button" @click="showPasswordFields = true"
                            class="bg-stone-100 hover:bg-stone-200 text-stone-800 font-black text-xs px-4 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5">
                            <span>🔑 Change Password</span>
                        </button>
                    </div>

                    <!-- Collapsible Password Input Fields -->
                    <div x-show="showPasswordFields" x-cloak class="space-y-4 pt-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-gray-800 uppercase tracking-wider">Set New Password</span>
                            <button type="button" @click="showPasswordFields = false"
                                class="text-xs text-stone-400 hover:text-stone-600 font-bold underline cursor-pointer">
                                Cancel Password Change
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Current Password
                                    *</label>
                                <input type="password" name="current_password" placeholder="Current password"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-900 outline-none focus:ring-2 focus:ring-green-800">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">New Password
                                    *</label>
                                <input type="password" name="new_password" placeholder="Min 6 characters"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-900 outline-none focus:ring-2 focus:ring-green-800">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Confirm New Password
                                    *</label>
                                <input type="password" name="new_password_confirmation" placeholder="Repeat new password"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-900 outline-none focus:ring-2 focus:ring-green-800">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 3: WhatsApp Alerts & Auto-Approval Settings -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200 shadow-xs space-y-6">
                <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                    <span class="text-lg">💬</span>
                    <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider">WhatsApp Notifications &
                        Automation Rules</h2>
                </div>

                <!-- WhatsApp Alert Number -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-black text-gray-800 uppercase tracking-wider">
                        WhatsApp Admin Notification Number (with Country Code)
                    </label>
                    <p class="text-xs text-gray-500">Every customer quotation and order confirmation will be sent as an
                        instant alert to this phone.</p>
                    <div class="max-w-md">
                        <input type="text" name="whatsapp_admin_number"
                            value="{{ old('whatsapp_admin_number', $adminPhone ?? '917758943614') }}" required
                            placeholder="e.g. 917758943614"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-bold font-mono outline-none focus:ring-2 focus:ring-green-800">
                    </div>
                </div>

                <hr class="border-gray-100">

                <!-- Auto-Approval Quantity Threshold -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-black text-gray-800 uppercase tracking-wider">
                            Minimum Cart Quantity for Auto-Approval
                        </label>
                        <p class="text-xs text-gray-500">
                            If total units in a customer order meet or exceed this quantity, the quotation is automatically
                            marked <strong>Approved</strong>.
                        </p>
                    </div>

                    <div class="max-w-xs">
                        <input type="number" name="auto_approval_min_qty"
                            value="{{ old('auto_approval_min_qty', $minAutoQty ?? 10) }}" min="1" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-bold outline-none focus:ring-2 focus:ring-green-800">
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="auto_approval_enabled" value="1" {{ !empty($autoApproveEnabled) ? 'checked' : '' }}
                            class="w-4 h-4 text-green-800 rounded-md border-gray-300 focus:ring-green-700">
                        <span class="text-xs font-bold text-gray-800">Enable Automatic Order Approval</span>
                    </label>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit"
                    class="bg-green-800 hover:bg-green-700 active:scale-98 text-white text-xs font-black px-8 py-3.5 rounded-xl transition shadow-md cursor-pointer">
                    Save All Settings →
                </button>
            </div>
        </form>

    </div>

    <!-- QR Modal Logic Script -->
    <script>
        async function loadWhatsAppQR() {
            const modal = document.getElementById('qrModal');
            const container = document.getElementById('qrImageContainer');
            const badge = document.getElementById('qrStatusBadge');

            modal.classList.remove('hidden');
            container.innerHTML = '<div class="flex flex-col items-center gap-2"><span class="w-6 h-6 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin"></span><span class="text-xs text-stone-500 font-medium">Fetching live session status...</span></div>';

            try {
                const res = await fetch("{{ route('admin.whatsapp.qr') }}");
                const data = await res.json();

                if (data.qrcode) {
                    badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span><span>Scan QR Code</span>';
                    badge.className = "inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-amber-200 rounded-full text-[11px] font-bold text-amber-800 shadow-2xs";
                    container.innerHTML = `<img src="${data.qrcode}" class="w-60 h-60 bg-white p-2.5 rounded-2xl shadow-md border border-stone-200 mx-auto" alt="WhatsApp Link QR">`;
                } else if (data.state === 'CONNECTED' || data.state === 'inChat' || data.state === 'isLogged') {
                    badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500"></span><span>Device Connected & Ready</span>';
                    badge.className = "inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-emerald-200 rounded-full text-[11px] font-bold text-emerald-800 shadow-2xs";
                    container.innerHTML = `
                        <div class="bg-white p-5 rounded-2xl border border-emerald-200 shadow-xs max-w-sm mx-auto text-center space-y-1.5">
                            <div class="text-3xl">✅</div>
                            <h4 class="text-xs font-black text-emerald-900 uppercase">WhatsApp Gateway is Active</h4>
                            <p class="text-[11px] text-stone-500">Your device is linked. Background OTPs and notifications are delivering normally.</p>
                        </div>`;
                } else {
                    container.innerHTML = `
                        <div class="bg-amber-50 p-4 rounded-xl border border-amber-200 text-xs text-amber-800">
                            Status: <strong>${data.state || 'Initializing'}</strong>. Please verify that your gateway is running on port 21465.
                        </div>`;
                }
            } catch (err) {
                container.innerHTML = `
                    <div class="bg-red-50 p-4 rounded-xl border border-red-200 text-xs text-red-700">
                        Failed to reach WhatsApp Gateway. Ensure <code>npm start</code> is running in <code>patsons-wa-gateway</code>.
                    </div>`;
            }
        }
    </script>
@endsection