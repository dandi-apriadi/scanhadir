<div class="min-h-screen bg-[#0f172a] flex flex-col items-center justify-center p-4 relative overflow-hidden">
    <!-- Animated Background Blur -->
    <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute top-0 -right-4 w-72 h-72 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

    <div class="z-10 w-full max-w-lg">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white tracking-tight mb-2">ScanHadir</h1>
            <p class="text-slate-400 text-lg">Sistem Presensi QR Sekolah Pintar</p>
        </div>

        <!-- Scanner Card -->
        <div class="glass p-6 rounded-3xl shadow-2xl border border-white/10 mb-6">
            <div id="reader" class="rounded-2xl overflow-hidden border-4 border-slate-700/50"></div>
            
            <div class="mt-8 text-center">
                <div @class([
                    'inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition-all duration-300',
                    'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' => $status === 'success',
                    'bg-rose-500/10 text-rose-400 border border-rose-500/20' => $status === 'error',
                    'bg-blue-500/10 text-blue-400 border border-blue-500/20' => $status === 'info',
                ])>
                    <span class="mr-2 h-2 w-2 rounded-full bg-current animate-pulse"></span>
                    {{ $message }}
                </div>
            </div>
        </div>

        <!-- Feedback Overlay (Dynamic) -->
        <div id="result-overlay" class="hidden glass fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-950/80 backdrop-blur-xl">
            <div class="text-center transform transition-all">
                <div class="w-24 h-24 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-[0_0_50px_rgba(16,185,129,0.3)]">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 id="student-name" class="text-3xl font-bold text-white mb-1">Nama Siswa</h2>
                <p id="student-class" class="text-xl text-slate-400 mb-8">Kelas</p>
                <div class="text-emerald-400 text-lg font-medium animate-bounce">Berhasil Presensi!</div>
            </div>
        </div>
    </div>

    <!-- Assets & Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener('livewire:navigated', () => {
            const html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 300, height: 300 } };

            const successAudio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
            const errorAudio = new Audio('https://assets.mixkit.co/active_storage/sfx/2873/2873-preview.mp3');

            html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                // Throttle scanning
                html5QrCode.pause();
                @this.processScan(decodedText);
            });

            Livewire.on('scan-success', (data) => {
                successAudio.play();
                document.getElementById('student-name').innerText = data.name;
                document.getElementById('student-class').innerText = data.class;
                const overlay = document.getElementById('result-overlay');
                overlay.classList.remove('hidden');
                
                setTimeout(() => {
                    overlay.classList.add('hidden');
                    html5QrCode.resume();
                }, 3000);
            });

            Livewire.on('scan-failed', () => {
                errorAudio.play();
                setTimeout(() => {
                    html5QrCode.resume();
                }, 2000);
            });
        });
    </script>

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
        #reader { border: none !important; }
        #reader__dashboard { display: none !important; }
        #reader video { border-radius: 1rem; }
    </style>
</div>
