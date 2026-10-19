<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PatSons Admin Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-50 text-gray-900 min-h-screen flex antialiased">

    <!-- Sidebar -->
    <aside
        class="w-64 bg-green-950 text-white flex flex-col justify-between p-5 shrink-0 min-h-screen shadow-xl sticky top-0 h-screen">
        <div class="space-y-6">
            <div class="flex items-center gap-3 px-2 border-b border-white/10 pb-4">
                <span class="text-2xl">🌴</span>
                <div>
                    <h2 class="font-black text-sm tracking-wider uppercase text-amber-400">PatSons Admin</h2>
                    <span class="text-[10px] text-green-300">Morjim, Goa Portal</span>
                </div>
            </div>

            <nav class="space-y-1 text-xs font-bold">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-green-800 text-white shadow-xs' : 'text-green-100/70 hover:bg-white/5 hover:text-white' }}">
                    <span>📊</span> <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.orders') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.orders*') ? 'bg-green-800 text-white shadow-xs' : 'text-green-100/70 hover:bg-white/5 hover:text-white' }}">
                    <span>🛒</span> <span>Orders & Quotations</span>
                </a>
                <a href="{{ route('admin.products') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.products*') ? 'bg-green-800 text-white shadow-xs' : 'text-green-100/70 hover:bg-white/5 hover:text-white' }}">
                    <span>📦</span> <span>Product Catalog</span>
                </a>
                <a href="{{ route('admin.users') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users*') ? 'bg-green-800 text-white shadow-xs' : 'text-green-100/70 hover:bg-white/5 hover:text-white' }}">
                    <span>👥</span> <span>Customer Directory</span>
                </a>
                <a href="{{ route('admin.settings') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.settings*') ? 'bg-green-800 text-white shadow-xs' : 'text-green-100/70 hover:bg-white/5 hover:text-white' }}">
                    <span>⚙️</span> <span>Store Settings & WhatsApp</span>
                </a>
            </nav>
        </div>

        <!-- Live WhatsApp Gateway Status Card -->
        <div x-data="whatsappGatewayWatcher()" x-init="checkGateway()"
            class="my-4 p-3.5 bg-white/5 rounded-2xl border border-white/10 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-green-300">WA Gateway</span>
                <span class="flex h-2 w-2 relative">
                    <span x-show="connected" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span :class="connected ? 'bg-emerald-400' : 'bg-amber-400'" class="relative inline-flex rounded-full h-2 w-2"></span>
                </span>
            </div>
            <div class="flex items-center justify-between text-[11px]">
                <span class="text-white/80 font-bold" x-text="statusText">Checking...</span>
                <button type="button" @click="checkGateway()" class="text-amber-300 hover:text-amber-200 text-xs cursor-pointer p-0.5" title="Refresh Status">↻</button>
            </div>
            <a href="{{ route('admin.settings') }}" class="block text-[10px] text-green-400 hover:text-white font-bold pt-1.5 border-t border-white/10">
                Configure / Scan QR →
            </a>
        </div>

        <div class="border-t border-white/10 pt-4 space-y-2 text-xs">
            <a href="{{ route('home') }}" target="_blank" class="text-green-300 hover:underline block font-bold">🌐 View
                Live Store ↗</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left text-red-300 hover:text-red-100 font-bold py-1 transition cursor-pointer">🚪 Sign
                    Out</button>
            </form>
        </div>
    </aside>

    <!-- Main Content Stage -->
    <main class="flex-1 p-6 sm:p-10 overflow-y-auto max-w-full">
        <!-- Admin Top Navigation Bar -->
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-gray-200">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500">Logged in as:</span>
                <span class="text-xs font-black text-gray-900 bg-gray-200/80 px-2.5 py-1 rounded-lg">{{ auth()->user()->name ?? 'Administrator' }}</span>
                <span class="text-gray-300">•</span>
                <span class="text-[11px] text-gray-500 font-medium">Dev Can Fruit Products (Morjim Factory)</span>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.settings') }}"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-bold text-gray-700 shadow-2xs hover:border-emerald-300 transition">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>WA Gateway: Port 21465</span>
                </a>
                <a href="{{ route('home') }}" target="_blank"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-900 text-white text-xs font-bold shadow-xs hover:bg-green-800 transition">
                    <span>🌐 View Storefront</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div
                class="mb-6 bg-emerald-100 border border-emerald-300 text-emerald-900 px-4 py-3 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
                <span>✓ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-emerald-700 hover:text-emerald-950 font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div
                class="mb-6 bg-red-100 border border-red-300 text-red-900 px-4 py-3 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
                <span>⚠️ {{ session('error') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-red-700 hover:text-red-950 font-bold">&times;</button>
            </div>
        @endif

        @yield('admin_content')
        @yield('content')
    </main>

    <script>
        function whatsappGatewayWatcher() {
            return {
                connected: false,
                statusText: 'Checking...',
                async checkGateway() {
                    try {
                        let res = await fetch('{{ route('admin.whatsapp.qr') }}');
                        let data = await res.json();
                        if (data.status === 'success' && (data.state === 'CONNECTED' || data.state === 'isLogged')) {
                            this.connected = true;
                            this.statusText = '🟢 Online';
                        } else if (data.qrcode) {
                            this.connected = false;
                            this.statusText = '🟡 Scan QR Code';
                        } else {
                            this.connected = false;
                            this.statusText = '🔴 Disconnected';
                        }
                    } catch (e) {
                        this.connected = false;
                        this.statusText = '⚪ Port 21465 Offline';
                    }
                }
            };
        }
    </script>
</body>

</html>