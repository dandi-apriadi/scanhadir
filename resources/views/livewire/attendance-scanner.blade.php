<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 flex flex-col items-center justify-center p-4 relative overflow-hidden">
    <!-- Animated Background Blur -->
    <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute top-0 -right-4 w-72 h-72 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

    <div class="z-10 w-full max-w-lg">
        <!-- Active Session Banner -->
        @if($activeSessionInfo)
            <div class="mb-6 backdrop-blur-md bg-emerald-500/10 border border-emerald-500/30 p-4 rounded-2xl shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-400 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-emerald-300 truncate">{{ $activeSessionInfo['subject_name'] }} ({{ $activeSessionInfo['subject_code'] }})</p>
                        <p class="text-xs text-emerald-400/70">{{ $activeSessionInfo['class_name'] }} • {{ $activeSessionInfo['start_time'] }} - {{ $activeSessionInfo['end_time'] }}</p>
                    </div>
                    <span class="px-2 py-1 bg-emerald-500/20 text-emerald-300 rounded-lg text-[10px] font-bold uppercase">{{ $activeSessionInfo['source'] === 'auto_schedule' ? 'AUTO' : 'MANUAL' }}</span>
                </div>
            </div>
        @else
            <div class="mb-6 backdrop-blur-md bg-amber-500/10 border border-amber-500/30 p-4 rounded-2xl shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-amber-300">Tidak Ada Sesi Aktif</p>
                        <p class="text-xs text-amber-400/70">Mulai sesi dari halaman Mata Kuliah Saya</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="text-center mb-8">
            <x-app-logo size="w-24 h-24" class="mx-auto mb-5 ring-4 ring-white/10 shadow-2xl shadow-indigo-500/20" />
            <h1 class="text-5xl font-bold text-white tracking-tight mb-2">ScanHadir</h1>
            <p class="text-slate-400 text-lg">Sistem Presensi QR Sekolah Pintar</p>
            <div class="mt-4 flex items-center justify-center gap-4 text-sm text-slate-400">
                <span>📍 Tanggal: {{ now()->format('d M Y') }}</span>
                <span>⏰ {{ now()->format('H:i:s') }}</span>
            </div>
        </div>

        <!-- Scanner Card -->
        <div class="backdrop-blur-md bg-white/10 border border-white/20 p-6 rounded-3xl shadow-2xl mb-6">
            <div id="reader" class="rounded-2xl overflow-hidden border-2 border-indigo-500/50 aspect-square"></div>
            
            <!-- Status Message -->
            <div class="mt-8">
                <div @class([
                    'flex items-center gap-3 p-4 rounded-xl transition-all duration-300',
                    'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' => $status === 'success',
                    'bg-rose-500/20 text-rose-300 border border-rose-500/30' => $status === 'error',
                    'bg-blue-500/20 text-blue-300 border border-blue-500/30' => $status === 'info',
                    'bg-amber-500/20 text-amber-300 border border-amber-500/30' => $status === 'warning',
                ])>
                    @if($status === 'success')
                        <svg class="w-5 h-5 flex-shrink-0 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    @elseif($status === 'error')
                        <svg class="w-5 h-5 flex-shrink-0 animate-bounce" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    @else
                        <svg class="w-5 h-5 flex-shrink-0 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    @endif
                    <span class="text-sm font-medium">{{ $message }}</span>
                </div>
            </div>

            <!-- Stats -->
            @if($scanCount > 0)
            <div class="mt-6 grid grid-cols-2 gap-4 p-4 bg-white/5 rounded-xl border border-white/10">
                <div class="text-center">
                    <div class="text-3xl font-bold text-indigo-400">{{ $scanCount }}</div>
                    <div class="text-xs text-slate-400 mt-1">Scan Hari Ini</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-slate-300">{{ $lastScanTime?->format('H:i:s') ?? '-' }}</div>
                    <div class="text-xs text-slate-400 mt-1">Scan Terakhir</div>
                </div>
            </div>
            @endif
        </div>

        <!-- Help Text -->
        <div class="text-center text-sm text-slate-400">
            <p>🎯 Posisikan QR Code di depan kamera untuk memindai kehadiran</p>
            <p class="mt-1">✅ Scan masuk saat tiba • 🚪 Scan pulang saat pergi</p>
        </div>
    </div>

    <!-- Success Overlay -->
    <div id="result-overlay" class="hidden fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-950/90 backdrop-blur-xl animate-in fade-in duration-200">
        <div class="text-center transform transition-all">
            <!-- Check-in Success -->
            <div id="checkin-overlay" class="hidden">
                <div class="w-28 h-28 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-[0_0_50px_rgba(16,185,129,0.4)] animate-pulse">
                    <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 id="student-name" class="text-4xl font-bold text-white mb-1">Nama Siswa</h2>
                <p id="student-class" class="text-xl text-slate-400 mb-4">Kelas</p>
                <p class="text-emerald-400 text-lg font-medium animate-bounce">✓ Absen Masuk Berhasil</p>
                <p class="text-slate-400 text-sm mt-3">Waktu: <span id="checkin-time">--:--:--</span></p>
            </div>

            <!-- Check-out Success -->
            <div id="checkout-overlay" class="hidden">
                <div class="w-28 h-28 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-[0_0_50px_rgba(59,130,246,0.4)] animate-pulse">
                    <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <h2 id="student-name-out" class="text-4xl font-bold text-white mb-1">Nama Siswa</h2>
                <p id="student-class-out" class="text-xl text-slate-400 mb-4">Kelas</p>
                <p class="text-blue-400 text-lg font-medium animate-bounce">✓ Absen Pulang Berhasil</p>
                <p class="text-slate-400 text-sm mt-3">Waktu: <span id="checkout-time">--:--:--</span></p>
            </div>
        </div>
    </div>

    <!-- Student Verification Modal (NEW: Priority 1 Feature) -->
    @if($awaitingConfirmation && $pendingStudentDetails)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/95 backdrop-blur-sm animate-in fade-in duration-200">
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl shadow-2xl max-w-md w-full p-8 animate-in zoom-in-50 duration-300">
            <!-- Header -->
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-white mb-2">Verifikasi Identitas</h2>
                <p class="text-sm text-slate-400">Pastikan ini adalah siswa yang benar sebelum melanjutkan</p>
            </div>

            <!-- Student Photo (if available) -->
            @if($pendingStudentDetails['has_photo'])
            <div class="mb-6">
                <img src="{{ $pendingStudentDetails['photo_url'] }}" 
                     alt="{{ $pendingStudentDetails['name'] }}"
                     class="w-40 h-40 rounded-2xl object-cover mx-auto border-4 border-indigo-500/50 shadow-[0_0_30px_rgba(99,102,241,0.3)]">
            </div>
            @else
            <div class="mb-6 flex justify-center">
                <div class="w-40 h-40 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center border-4 border-indigo-500/50 shadow-[0_0_30px_rgba(99,102,241,0.3)]">
                    <svg class="w-20 h-20 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
            @endif

            <!-- Student Details -->
            <div class="space-y-4 mb-6 p-4 bg-white/5 rounded-2xl border border-white/10">
                <div>
                    <p class="text-xs text-slate-400 uppercase font-bold mb-1">Nama Siswa</p>
                    <p class="text-lg font-bold text-white">{{ $pendingStudentDetails['name'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase font-bold mb-1">Kelas</p>
                    <p class="text-base font-semibold text-indigo-300">{{ $pendingStudentDetails['class'] ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase font-bold mb-1">NISN</p>
                    <p class="text-sm font-mono text-slate-300">{{ $pendingStudentDetails['nisn'] }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-2 gap-3">
                <button wire:click="cancelStudent" 
                        class="px-4 py-3 bg-red-500/20 hover:bg-red-500/30 border border-red-500/50 text-red-300 font-bold rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </button>
                <button wire:click="confirmStudent" 
                        class="px-4 py-3 bg-emerald-500/20 hover:bg-emerald-500/30 border border-emerald-500/50 text-emerald-300 font-bold rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Benar
                </button>
            </div>

            <!-- Warning -->
            <p class="text-center text-xs text-amber-400 mt-4">⚠️ Pastikan identitas sebelum menekan tombol "Benar"</p>
        </div>
    </div>
    @endif

    <!-- Scripts -->
    @vite('resources/js/app.js')
    <script>
        let qrScanner = null;
        let isProcessing = false;
        let activeCameraIsFront = false;
        const SCAN_COOLDOWN = 1500; // ms
        const reader = document.getElementById('reader');

        document.addEventListener('livewire:navigated', () => {
            initQrScanner();
            startTimeClock();
        });

        function getScannerFormats() {
            if (typeof Html5QrcodeSupportedFormats === 'undefined') {
                return undefined;
            }

            const resolved = Object.values(Html5QrcodeSupportedFormats)
                .filter((format) => typeof format === 'number');

            const uniqueFormats = [...new Set(resolved)];

            return uniqueFormats.length > 0 ? uniqueFormats : undefined;
        }

        function getScannerConfig() {
            const viewportWidth = Math.max(window.innerWidth || 0, 360);
            const viewportHeight = Math.max(window.innerHeight || 0, 480);
            const scanWidth = Math.min(560, Math.floor(viewportWidth * 0.9));
            const scanHeight = Math.max(190, Math.min(320, Math.floor(viewportHeight * 0.42)));

            const config = {
                fps: 18,
                qrbox: { width: scanWidth, height: scanHeight },
                rememberLastUsedCamera: true,
                aspectRatio: 1.333,
                disableFlip: false,
                showTorchButtonIfSupported: true,
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: false,
                },
            };

            const formats = getScannerFormats();
            if (formats) {
                config.formatsToSupport = formats;
            }

            return config;
        }

        async function initQrScanner() {
            const html5QrCode = new Html5Qrcode("reader");
            qrScanner = html5QrCode;
            const config = getScannerConfig();

            const cameras = await Html5Qrcode.getCameras();
            const rearRegex = /(rear|back|environment|traseira|tr\u00e1s)/i;
            const frontRegex = /(front|user|facetime|webcam|integrated)/i;
            const selectedCamera = cameras.find((camera) => rearRegex.test(camera.label || '')) || cameras[0] || null;
            if (selectedCamera) {
                activeCameraIsFront = frontRegex.test(selectedCamera.label || '');
                applyMirrorFix(activeCameraIsFront);
            }

            const successAudio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
            const errorAudio = new Audio('https://assets.mixkit.co/active_storage/sfx/2873/2873-preview.mp3');

            const onScanSuccess = (decodedText) => {
                if (isProcessing) return;
                isProcessing = true;

                html5QrCode.pause();
                @this.processScan(decodedText);

                setTimeout(() => {
                    html5QrCode.resume();
                    isProcessing = false;
                }, SCAN_COOLDOWN);
            };

            try {
                await html5QrCode.start(selectedCamera ? selectedCamera.id : { facingMode: { exact: "environment" } }, config, onScanSuccess);
            } catch (envError) {
                try {
                    activeCameraIsFront = true;
                    applyMirrorFix(true);
                    await html5QrCode.start({ facingMode: "user" }, config, onScanSuccess);
                } catch (fallbackError) {
                    @this.dispatch('scan-failed');
                }
            }

            Livewire.on('scan-success', (data) => {
                successAudio.play();
                showSuccessOverlay(data.action, data.name, data.class);
            });

            Livewire.on('scan-failed', () => {
                errorAudio.play();
            });

            Livewire.on('scan-info', () => {
                // Info sound could be added
            });
        }

        function showSuccessOverlay(action, name, className) {
            const overlay = document.getElementById('result-overlay');
            const checkinOverlay = document.getElementById('checkin-overlay');
            const checkoutOverlay = document.getElementById('checkout-overlay');

            checkinOverlay.classList.add('hidden');
            checkoutOverlay.classList.add('hidden');

            if (action === 'checkin') {
                checkinOverlay.classList.remove('hidden');
                document.getElementById('student-name').innerText = name;
                document.getElementById('student-class').innerText = className;
                const now = new Date();
                document.getElementById('checkin-time').innerText = now.toLocaleTimeString('id-ID');
            } else if (action === 'checkout') {
                checkoutOverlay.classList.remove('hidden');
                document.getElementById('student-name-out').innerText = name;
                document.getElementById('student-class-out').innerText = className;
                const now = new Date();
                document.getElementById('checkout-time').innerText = now.toLocaleTimeString('id-ID');
            }

            overlay.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 3500);
        }

        function applyMirrorFix(isFrontCamera) {
            if (!reader) return;

            if (isFrontCamera) {
                reader.classList.add('camera-unmirror');
            } else {
                reader.classList.remove('camera-unmirror');
            }
        }

        function startTimeClock() {
            setInterval(() => {
                const now = new Date();
                const timeString = now.toLocaleTimeString('id-ID');
                // Update time display if needed
            }, 1000);
        }
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
        #reader { border: none !important; width: 100% !important; height: 100% !important; }
        #reader > div,
        #reader__scan_region { width: 100% !important; height: 100% !important; }
        #reader__scan_region { display: flex !important; align-items: center; justify-content: center; overflow: hidden; }
        #reader__dashboard { display: none !important; }
        #reader video,
        #reader__scan_region video,
        #reader__scan_region img { border-radius: 1rem; width: 100% !important; height: 100% !important; object-fit: cover; }
        #reader.camera-unmirror video,
        #reader.camera-unmirror #reader__scan_region video,
        #reader.camera-unmirror #reader__scan_region img { transform: scaleX(-1); }
    </style>
</div>
