@extends('layouts.dashboard')

@php
    $userRole = auth()->user()?->role?->name ?? 'guest';
    $rolePrefix = match($userRole) {
        'admin' => 'admin',
        'hr' => 'hr',
        default => 'kadidat',
    };
    $roleLabel = match($userRole) {
        'admin' => 'Administrator',
        'hr' => 'HR Recruiter',
        default => 'Pelamar / Job Seeker',
    };
@endphp

@section('title', 'Daftar Lowongan Kerja')
@section('role-label', $roleLabel)
@section('page-title', 'Daftar Lowongan Pekerjaan (Job Vacancies)')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Overview Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Daftar Lowongan Kerja</h2>
            <p class="text-xs text-slate-500 mt-1">
                @if($userRole === 'kandidat')
                    Temukan peluang karir terbaik dan lamar posisi yang sesuai dengan keahlian serta minat Anda.
                @else
                    Kelola seluruh posisi lowongan pekerjaan, pantau jumlah pelamar yang masuk, dan publikasikan posisi baru.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if(in_array($userRole, ['admin', 'hr'], true))
                <a href="{{ route($rolePrefix . '.positions.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Buka Lowongan Baru</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-slate-500">Total Lowongan Aktif</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $totalActive }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-slate-500">Semua Lowongan</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalAll }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-slate-500">Departemen Tersedia</span>
            <div class="text-2xl font-black text-indigo-600 mt-1">{{ count($departments) }}</div>
        </div>
        @if($userRole === 'kandidat')
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-semibold text-blue-600">Lamaran Anda</span>
                <div class="text-2xl font-black text-blue-700 mt-1">{{ count($appliedPositionIds) }}</div>
            </div>
        @else
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-semibold text-purple-600">Total Pelamar Terdaftar</span>
                <div class="text-2xl font-black text-purple-700 mt-1">{{ \App\Models\CandidateApplication::count() }}</div>
            </div>
        @endif
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">
        <form method="GET" action="{{ route($rolePrefix . '.positions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Cari Posisi / Kata Kunci</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $searchTerm }}" placeholder="Judul, departemen, lokasi..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Department Filter -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Departemen</label>
                <select name="department" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ $selectedDepartment === $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Job Type Filter -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tipe Pekerjaan</label>
                <select name="type" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    <option value="">Semua Tipe</option>
                    <option value="Full-time" {{ $selectedType === 'Full-time' ? 'selected' : '' }}>Full-time</option>
                    <option value="Part-time" {{ $selectedType === 'Part-time' ? 'selected' : '' }}>Part-time</option>
                    <option value="Contract" {{ $selectedType === 'Contract' ? 'selected' : '' }}>Contract</option>
                    <option value="Remote" {{ $selectedType === 'Remote' ? 'selected' : '' }}>Remote</option>
                    <option value="Internship" {{ $selectedType === 'Internship' ? 'selected' : '' }}>Internship</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow transition text-center">
                    Filter
                </button>
                @if($searchTerm || $selectedDepartment || $selectedType || $selectedStatus)
                    <a href="{{ route($rolePrefix . '.positions.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs transition" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Positions Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($positions as $pos)
            @php
                $isApplied = in_array($pos->id, $appliedPositionIds, true);
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 hover:border-indigo-300 hover:shadow-lg transition duration-200 p-6 flex flex-col justify-between group">
                <div>
                    <!-- Card Top Badges -->
                    <div class="flex items-center justify-between gap-2 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700">
                                {{ $pos->department }}
                            </span>
                            @if($pos->is_internal)
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 border border-purple-200">
                                    Internal
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $pos->type }}
                            </span>
                            @if(in_array($userRole, ['admin', 'hr'], true))
                                <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded
                                    {{ $pos->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $pos->status }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Job Title -->
                    <h3 class="text-base font-bold text-slate-900 mt-3 group-hover:text-indigo-600 transition line-clamp-2">
                        <a href="{{ route($rolePrefix . '.positions.show', $pos->id) }}">
                            {{ $pos->title }}
                        </a>
                    </h3>

                    <!-- Location & Level -->
                    <div class="text-xs text-slate-500 mt-1.5 flex items-center gap-2 flex-wrap">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ $pos->location }}</span>
                        </span>
                        <span>&bull;</span>
                        <span>{{ $pos->experience_level }}</span>
                    </div>

                    <!-- Description Preview -->
                    <p class="text-xs text-slate-600 mt-3 line-clamp-2 leading-relaxed">
                        {{ $pos->description }}
                    </p>

                    <!-- Salary & Quota Pill Box -->
                    <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                        <div>
                            <span class="text-[10px] text-slate-400 font-semibold block">Kisaran Gaji</span>
                            <span class="font-bold text-slate-900 text-xs">
                                Rp {{ number_format($pos->salary_min, 0, ',', '.') }} - {{ number_format($pos->salary_max, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 font-semibold block">Kuota & Pelamar</span>
                            <span class="font-bold text-indigo-700 text-xs">
                                {{ $pos->applications_count }} / {{ $pos->quota }} Kuota
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card Footer & Action Buttons -->
                <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-[11px] text-slate-400">
                        @if($pos->deadline)
                            Batas: {{ \Carbon\Carbon::parse($pos->deadline)->format('d M Y') }}
                        @else
                            Buka Terus
                        @endif
                    </span>

                    <div class="flex items-center gap-2">
                        @if($userRole === 'kandidat' && $isApplied)
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Sudah Dilamar</span>
                            </span>
                        @endif

                        @if($userRole === 'kandidat' && !$isApplied)
                            @if(($hasActiveOffering ?? false) && !$pos->is_internal)
                                <span title="Anda sudah dalam tahap offering dan hanya dapat melamar lowongan internal"
                                      class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 font-bold text-xs border border-amber-200 cursor-not-allowed">
                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <span>Tahap Offering</span>
                                </span>
                            @else
                                <a href="{{ route('kadidat.positions.show', $pos->id) }}#apply-section"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    <span>Lamar</span>
                                </a>
                            @endif
                        @endif

                        <a href="{{ route($rolePrefix . '.positions.show', $pos->id) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 font-bold text-xs transition">
                            <span>Detail</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>

                        @if(in_array($userRole, ['admin', 'hr'], true))
                            <a href="{{ route($rolePrefix . '.positions.edit', $pos->id) }}"
                               title="Edit Lowongan"
                               class="inline-flex items-center gap-1 p-1.5 sm:px-2.5 sm:py-1.5 rounded-xl bg-amber-50 hover:bg-amber-500 hover:text-white text-amber-700 border border-amber-200 hover:border-amber-500 font-bold text-xs transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <span class="hidden sm:inline">Edit</span>
                            </a>

                            <form action="{{ route($rolePrefix . '.positions.destroy', $pos->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lowongan pekerjaan {{ addslashes($pos->title) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus Lowongan" class="p-1.5 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 border border-slate-200 hover:border-rose-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-1 md:col-span-2 lg:col-span-3 py-16 bg-white rounded-2xl border border-slate-200 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Tidak ada lowongan yang sesuai</h3>
                <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci atau filter departemen Anda.</p>
                @if($searchTerm || $selectedDepartment || $selectedType || $selectedStatus)
                    <a href="{{ route($rolePrefix . '.positions.index') }}" class="inline-block mt-3 px-3.5 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold hover:bg-indigo-100 transition">
                        Reset Filter
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($positions->hasPages())
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
            {{ $positions->links() }}
        </div>
    @endif

</div>
@endsection
