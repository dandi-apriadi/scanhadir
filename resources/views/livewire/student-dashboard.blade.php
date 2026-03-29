<div class="min-h-screen bg-slate-900 text-slate-100 flex flex-col p-6 animate-fadeIn">
    <!-- Navbar -->
    <header class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Halo, {{ Auth::user()->name }} 👋</h1>
            <p class="text-slate-400">Selamat datang di portal ScanHadir.</p>
        </div>
        <div class="h-12 w-12 rounded-full overflow-hidden border-2 border-indigo-500 shadow-lg shadow-indigo-500/20">
            <img src="{{ $student->photo_path ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=6366f1&color=fff' }}" alt="Avatar" class="h-full w-full object-cover">
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- QR Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass p-8 rounded-[2.5rem] flex flex-col items-center text-center shadow-2xl relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-600 rounded-full blur-[80px] opacity-40"></div>
                
                <h2 class="text-xl font-semibold mb-6 z-10">Kartu Digital Anda</h2>
                
                <div class="bg-white p-4 rounded-3xl shadow-xl shadow-cyan-500/20 mb-6 z-10 transition-transform hover:scale-105 duration-300">
                    {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->format('svg')->generate($student->qr_code) !!}
                </div>

                <div class="z-10 bg-slate-800/50 px-6 py-2 rounded-full text-sm font-mono border border-white/5 mb-8">
                    {{ $student->qr_code }}
                </div>

                <div class="w-full space-y-1 z-10">
                    <div class="text-slate-400 text-sm">NISN</div>
                    <div class="text-lg font-bold tracking-wide">{{ $student->nisn }}</div>
                    <div class="pt-2 text-indigo-400 font-medium tracking-wider">{{ $student->class->name }}</div>
                </div>
            </div>
            
            <a href="{{ route('scan') }}" target="_blank" class="block w-full text-center py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-2xl transition-all shadow-lg shadow-indigo-600/30">
                Uji Coba Scan QR
            </a>
        </div>

        <!-- History Content -->
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-bold mb-6">Riwayat Kehadiran Terakhir</h2>
            
            <div class="space-y-4">
                @forelse($history as $record)
                <div class="glass p-5 rounded-2xl flex items-center justify-between transition-all hover:bg-white/10 group">
                    <div class="flex items-center gap-4">
                        <div @class([
                            'h-12 w-12 rounded-xl flex items-center justify-center text-xl font-bold',
                            'bg-emerald-500/10 text-emerald-400' => $record->status === 'present',
                            'bg-amber-500/10 text-amber-400' => $record->status === 'late',
                            'bg-rose-500/10 text-rose-400' => $record->status === 'absent',
                            'bg-blue-500/10 text-blue-400' => in_array($record->status, ['sick', 'excused']),
                        ])>
                            {{ substr(strtoupper($record->status), 0, 1) }}
                        </div>
                        <div>
                            <div class="font-semibold text-white group-hover:text-indigo-400 transition-colors">
                                {{ Carbon\Carbon::parse($record->date)->translatedFormat('d F Y') }}
                            </div>
                            <div class="text-sm text-slate-400">
                                @if($record->check_in)
                                    Masuk: {{ $record->check_in }} • Pulang: {{ $record->check_out ?? '--:--' }}
                                @else
                                    {{ ucfirst($record->status) }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div @class([
                        'px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider',
                        'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' => $record->status === 'present',
                        'bg-amber-500/20 text-amber-400 border border-amber-500/30' => $record->status === 'late',
                        'bg-rose-500/20 text-rose-400 border border-rose-500/30' => $record->status === 'absent',
                        'bg-blue-500/20 text-blue-400 border border-blue-500/30' => in_array($record->status, ['sick', 'excused']),
                    ])>
                        {{ $record->status }}
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-20 text-slate-500 border-2 border-dashed border-slate-800 rounded-3xl">
                    <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>Sama sekali belum ada riwayat kehadiran.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.6s ease-out forwards; }
    </style>
</div>
