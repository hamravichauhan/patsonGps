@extends('layouts.admin')

@section('admin_content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900">Customer & Wholesale Directory</h1>
                <p class="text-xs text-gray-500">Manage client accounts, review B2B verification requests, and grant wholesale factory pricing.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">
                    <span>🟢</span> <span>{{ \App\Models\User::where('is_wholesaler', true)->count() }} Wholesalers</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                    <span>⏳</span> <span>{{ \App\Models\User::where('wholesale_requested', true)->where('is_wholesaler', false)->count() }} Pending Requests</span>
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-bold text-emerald-800 flex items-center gap-2 shadow-xs">
                <span>✓</span> <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-gray-100 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 font-bold uppercase text-[10px]">
                            <th class="p-4">Customer / Partner</th>
                            <th class="p-4">Contact & Business</th>
                            <th class="p-4">Pricing Tier</th>
                            <th class="p-4 text-center">B2B Access Control</th>
                            <th class="p-4 text-right">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium">
                        @forelse($users as $u)
                            <tr class="hover:bg-gray-50/60 transition {{ $u->wholesale_requested && !$u->is_wholesaler ? 'bg-amber-50/30' : '' }}">
                                <td class="p-4">
                                    <div class="font-bold text-gray-900 text-sm">{{ $u->name }}</div>
                                    <div class="text-gray-400 text-[11px]">{{ $u->email }}</div>
                                    @if($u->is_admin)
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-md text-[9px] font-black bg-amber-100 text-amber-900">
                                            ★ Admin
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="font-mono font-bold text-gray-800">{{ $u->phone ?: 'No Phone' }}</div>
                                    @if($u->business_name)
                                        <div class="text-xs font-bold text-gray-700 mt-0.5">🏢 {{ $u->business_name }}</div>
                                    @endif
                                    @if($u->gstin)
                                        <div class="text-[10px] text-gray-400 font-mono">GST: {{ $u->gstin }}</div>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($u->is_wholesaler)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-900 shadow-2xs">
                                            <span>✓</span> <span>Wholesaler (B2B Pricing)</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600">
                                            <span>🛒</span> <span>Standard Retail (MRP)</span>
                                        </span>
                                    @endif

                                    @if($u->wholesale_requested && !$u->is_wholesaler)
                                        <div class="mt-1.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-400 text-gray-950 animate-pulse">
                                                ★ Wholesale Requested
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    @if(!$u->is_admin)
                                        <form method="POST" action="{{ route('admin.users.toggle-wholesaler', $u->id) }}">
                                            @csrf
                                            @if($u->is_wholesaler)
                                                <button type="submit" 
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition cursor-pointer"
                                                    onclick="return confirm('Revoke wholesale rates for {{ $u->name }}? They will only see retail MRP.')">
                                                    ✕ Revoke Wholesale
                                                </button>
                                            @else
                                                <button type="submit" 
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                                                    ✓ Approve Wholesale Pricing
                                                </button>
                                            @endif
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-[11px] italic">Admin Account</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right text-gray-400 text-[11px]">{{ $u->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400 font-bold">
                                    No customer records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
