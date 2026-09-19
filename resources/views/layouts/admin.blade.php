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
                    <span class="text-[10px] text-green-300">Morji, Goa Portal</span>
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

        <div class="border-t border-white/10 pt-4 space-y-2 text-xs">
            <a href="{{ route('home') }}" target="_blank" class="text-green-300 hover:underline block font-bold">🌐 View
                Live Store ↗</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left text-red-300 hover:text-red-100 font-bold py-1 transition">🚪 Sign
                    Out</button>
            </form>
        </div>
    </aside>

    <!-- Main Content Stage -->
    <main class="flex-1 p-6 sm:p-10 overflow-y-auto max-w-full">
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

</body>

</html>