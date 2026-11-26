<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PatSons Admin Portal</title>
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-50 text-gray-900 min-h-screen flex antialiased">

    <!-- Sidebar -->
    <aside
        class="w-64 bg-green-950 text-white flex flex-col justify-between p-5 shrink-0 min-h-screen shadow-xl sticky top-0 h-screen">
        <div class="space-y-6">
            <div class="flex items-center gap-3 px-2 border-b border-white/10 pb-4">
                <div class="w-9 h-9 rounded-xl bg-amber-400/10 border border-amber-400/20 flex items-center justify-center text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <h2 class="font-black text-sm tracking-wider uppercase text-amber-400">PatSons Admin</h2>
                    <span class="text-[10px] text-green-300">Morjim, Goa Portal</span>
                </div>
            </div>

            <nav class="space-y-1 text-xs font-bold">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-green-800 text-white shadow-xs' : 'text-green-100/70 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.orders') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.orders*') ? 'bg-green-800 text-white shadow-xs' : 'text-green-100/70 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Orders & Quotations</span>
                </a>
                <a href="{{ route('admin.products') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.products*') ? 'bg-green-800 text-white shadow-xs' : 'text-green-100/70 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>Product Catalog</span>
                </a>
                <a href="{{ route('admin.users') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users*') ? 'bg-green-800 text-white shadow-xs' : 'text-green-100/70 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Customer Directory</span>
                </a>
                <a href="{{ route('admin.settings') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.settings*') ? 'bg-green-800 text-white shadow-xs' : 'text-green-100/70 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Store Settings & WhatsApp</span>
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
            <a href="{{ route('home') }}" target="_blank" class="text-green-300 hover:underline block font-bold">View
                Live Store ↗</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left text-red-300 hover:text-red-100 font-bold py-1 transition cursor-pointer">Sign
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
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>WA Gateway: Port 21465</span>
                </a>
                <a href="{{ route('home') }}" target="_blank"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-900 text-white text-xs font-bold shadow-xs hover:bg-green-800 transition">
                    <span>View Storefront</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div
                class="mb-6 bg-emerald-100 border border-emerald-300 text-emerald-900 px-4 py-3 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-emerald-700 hover:text-emerald-950 font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div
                class="mb-6 bg-red-100 border border-red-300 text-red-900 px-4 py-3 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
                <span>{{ session('error') }}</span>
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
                            this.statusText = 'Online';
                        } else if (data.qrcode) {
                            this.connected = false;
                            this.statusText = 'Scan QR Code';
                        } else {
                            this.connected = false;
                            this.statusText = 'Disconnected';
                        }
                    } catch (e) {
                        this.connected = false;
                        this.statusText = 'Port 21465 Offline';
                    }
                }
            };
        }
    </script>
</body>

</html>