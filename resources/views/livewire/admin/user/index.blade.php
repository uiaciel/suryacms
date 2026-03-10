@extends('suryacms::layouts.app')
@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Manage Users</h1>
                <nav class="flex text-sm text-slate-500 mt-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 ">
                        <li><a href="/admin" class="hover:text-blue-600 transition-colors">Admin</a></li>
                        <li><i class="fas fa-chevron-right text-[10px] mx-2 text-slate-300"></i></li>
                        <li class="text-blue-600 font-medium">Users</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="/admin/users/create" class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25 group">
                    <i class="fas fa-plus mr-2 text-xs group-hover:rotate-90 transition-transform"></i>
                    New User
                </a>
            </div>
        </header>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">User Information</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Email Address</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Joined Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($users as $user)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-sm group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <span class="font-bold text-slate-700">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 font-medium">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">
                                    <span class="inline-flex items-center"><i class="far fa-calendar-alt mr-2 opacity-50"></i> {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-400 italic">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
