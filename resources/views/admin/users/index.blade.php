@extends('layouts.admin')

@section('admin_content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900">User & Admin Directory</h1>
            <p class="text-xs text-gray-500">View registered staff, admins, and account profiles.</p>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-2xs overflow-hidden">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 font-bold uppercase text-[10px]">
                        <th class="p-4">Name</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Role</th>
                        <th class="p-4">Registered Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @foreach($users as $u)
                        <tr>
                            <td class="p-4 font-bold text-gray-900">{{ $u->name }}</td>
                            <td class="p-4 text-gray-600">{{ $u->email }}</td>
                            <td class="p-4">
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $u->is_admin ? 'bg-amber-100 text-amber-900' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $u->is_admin ? '★ Administrator' : 'Customer / User' }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-400">{{ $u->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection