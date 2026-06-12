<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>ScanHadir - QR Scanner Console</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpeg') }}"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#2f1bc8",
                        "primary-container": "#493fdf",
                        "surface-variant": "#d5e3fc",
                    },
                    fontFamily: {
                        headline: ["Manrope"],
                        body: ["Inter"],
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .scan-line {
            height: 2px;
            background: linear-gradient(90deg, transparent, #493fdf, transparent);
            box-shadow: 0 0 15px #493fdf;
            animation: scan 3s ease-in-out infinite;
        }
        @keyframes scan {
            0%, 100% { top: 0%; }
            50% { top: 100%; }
        }
    </style>
    @vite('resources/js/app.js')
</head>
<body class="bg-slate-950 font-body text-white overflow-hidden">
    <!-- Top Bar -->
    <nav class="flex justify-between items-center w-full px-8 py-4 bg-slate-900/50 backdrop-blur-xl border-b border-white/5">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center hover:bg-white/10 transition-colors">
                <span class="material-symbols-outlined text-white">arrow_back</span>
            </a>
            <x-app-logo size="w-11 h-11" class="ring-white/10" />
            <div>
                <h1 class="text-xl font-bold font-headline tracking-tighter leading-none">ScanHadir</h1>
                <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mt-1">Scanner Engine v2.0</p>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2 px-4 py-2 bg-emerald-500/10 rounded-full border border-emerald-500/20">
                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">Sistem Online</span>
            </div>
        </div>
    </nav>

    <!-- Hidden NISN Input Form for Scanner -->
    <form id="scanForm" method="POST" action="{{ route('admin.attendance.scan') }}" class="sr-only">
        @csrf
        <input id="nisnInput" type="text" name="nisn" autofocus>
    </form>

    <!-- Error Banner -->
    <div id="errorBanner" class="hidden fixed top-20 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 px-6 py-3 bg-rose-500/20 border border-rose-500/40 rounded-2xl backdrop-blur-xl text-rose-300 text-sm font-semibold shadow-2xl">
        <span class="material-symbols-outlined text-lg text-rose-400">error</span>
        <span id="errorBannerText"></span>
        <button onclick="document.getElementById('errorBanner').classList.add('hidden')" class="ml-2 text-rose-400 hover:text-white transition-colors">
            <span class="material-symbols-outlined text-base">close</span>
        </button>
    </div>

    <main class="flex h-[calc(100vh-76px)] p-8 gap-8">
        <!-- Scanner Viewport -->
        <div class="flex-1 relative bg-black rounded-[40px] overflow-hidden border border-white/10 shadow-2xl flex items-center justify-center">
            <div id="reader" class="absolute inset-0 z-0"></div>

            <!-- Camera Error State / Manual Input Mode -->
            <div id="cameraError" class="hidden absolute inset-0 z-[5] flex flex-col items-center justify-center gap-6 bg-slate-950/90">
                <div class="w-16 h-16 rounded-full bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl text-indigo-400">qr_code_scanner</span>
                </div>
                <div class="text-center">
                    <p class="text-white font-bold text-lg font-headline">Mode Input Manual</p>
                    <p class="text-slate-400 text-xs mt-1 max-w-sm">Kamera tidak tersedia. Ketik NISN atau gunakan barcode scanner USB.</p>
                </div>

                <!-- Manual NISN Input -->
                <div class="w-full max-w-md px-8">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400">badge</span>
                        <input id="manualNisnInput" type="text" placeholder="Ketik atau scan NISN di sini..."
                            class="w-full pl-12 pr-28 py-4 bg-white/5 border border-white/10 rounded-2xl text-white text-lg font-bold font-headline placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition-all"
                            autocomplete="off">
                        <button onclick="submitManualNisn()" class="absolute right-2 top-1/2 -translate-y-1/2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-white text-xs font-bold uppercase tracking-widest transition-all">
                            Kirim
                        </button>
                    </div>
                    <p class="text-slate-600 text-[10px] mt-2 text-center uppercase tracking-widest">Tekan Enter untuk mengirim</p>
                </div>

                <button onclick="retryCamera()" class="px-5 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-slate-400 text-[10px] font-bold uppercase tracking-widest transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">videocam</span> Coba Aktifkan Kamera
                </button>
            </div>

            <!-- Simulated Video Feed -->
            <div class="absolute inset-0 opacity-25 mix-blend-overlay grayscale z-[1] pointer-events-none">
                <div class="w-full h-full bg-gradient-to-br from-indigo-900/20 to-slate-900/20"></div>
            </div>

            <!-- Focus Box -->
            <div id="focusBox" class="relative w-80 h-80 z-10 pointer-events-none">
                <div class="absolute top-0 left-0 w-16 h-16 border-t-4 border-l-4 border-primary rounded-tl-2xl"></div>
                <div class="absolute top-0 right-0 w-16 h-16 border-t-4 border-r-4 border-primary rounded-tr-2xl"></div>
                <div class="absolute bottom-0 left-0 w-16 h-16 border-b-4 border-l-4 border-primary rounded-bl-2xl"></div>
                <div class="absolute bottom-0 right-0 w-16 h-16 border-b-4 border-r-4 border-primary rounded-br-2xl"></div>
                
                <!-- Scanning Line -->
                <div class="absolute inset-4 rounded-xl overflow-hidden">
                    <div class="scan-line absolute w-full top-0"></div>
                </div>
            </div>

            <!-- Bottom Controls -->
            <div class="absolute bottom-10 z-20 flex items-center gap-4">
                <button id="focusButton" type="button" onclick="document.getElementById('nisnInput').focus()" class="px-6 py-3 bg-white/10 backdrop-blur-xl rounded-full text-xs font-bold uppercase tracking-widest hover:bg-white/20 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">videocam</span> Focus
                </button>
                <button id="resetButton" type="button" onclick="clearScannedData()" class="px-6 py-3 bg-white/10 backdrop-blur-xl rounded-full text-xs font-bold uppercase tracking-widest hover:bg-white/20 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">restart_alt</span> Reset
                </button>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="w-96 flex flex-col gap-6">
            <!-- Latest Scanned -->
            <div class="bg-white/5 border border-white/5 rounded-[32px] p-8 flex flex-col items-center text-center">
                <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-8">Terakhir Dipindai</h3>
                
                <div class="relative w-32 h-32 mb-6">
                    <div class="absolute inset-0 rounded-full border-2 border-primary animate-pulse"></div>
                    <div class="w-full h-full rounded-full bg-slate-800 flex items-center justify-center overflow-hidden border-4 border-slate-900">
                        <span class="material-symbols-outlined text-5xl text-slate-600" id="studentIcon">person</span>
                    </div>
                </div>

                <h4 class="text-2xl font-black font-headline text-white mb-1" id="studentName">-</h4>
                <p class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-8" id="studentClass">-</p>

                <div class="w-full grid grid-cols-2 gap-4">
                    <div class="p-4 bg-white/5 rounded-2xl text-left border border-white/5">
                        <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Waktu</p>
                        <p class="text-lg font-black font-headline" id="checkInTime">--:--</p>
                    </div>
                    <div class="p-4 bg-white/5 rounded-2xl text-left border border-white/5">
                        <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Status</p>
                        <p class="text-lg font-black font-headline" id="statusDisplay" style="color: #9ca3af;">SIAP</p>
                    </div>
                </div>
            </div>

            <!-- Quick Logs -->
            <div class="flex-1 bg-white/5 border border-white/5 rounded-[32px] p-8 overflow-hidden flex flex-col">
                <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-6 px-1">Log Sesi Ini</h3>
                <div id="scanLogs" class="space-y-4 overflow-y-auto">
                    <p class="text-center text-sm text-slate-400 py-8">Belum ada scan dalam sesi ini</p>
                </div>
            </div>
        </div>
    </main>

    <script>
        const nisnInput = document.getElementById('nisnInput');
        const studentName = document.getElementById('studentName');
        const studentClass = document.getElementById('studentClass');
        const checkInTime = document.getElementById('checkInTime');
        const statusDisplay = document.getElementById('statusDisplay');
        const scanLogs = document.getElementById('scanLogs');
        const reader = document.getElementById('reader');
        const focusButton = document.getElementById('focusButton');
        const resetButton = document.getElementById('resetButton');
        const manualNisnInput = document.getElementById('manualNisnInput');
        let html5QrScanner = null;
        let isProcessingScan = false;
        let activeCameraIsFront = false;
        let keyboardBuffer = '';
        let keyboardBufferTimeout = null;
        let lastSubmittedCode = '';
        let lastSubmittedAt = 0;
        let scannedStudents = [];
        const KEYBOARD_BUFFER_RESET_MS = 120;
        const DUPLICATE_SCAN_COOLDOWN_MS = 4000;
        const MIN_SCANNER_INPUT_LENGTH = 5;

        // Focus on page load
        window.addEventListener('load', () => {
            initializeCameraScanner();
            nisnInput.focus();
            setupUsbScannerCapture();
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
                qrbox: {
                    width: scanWidth,
                    height: scanHeight,
                },
                rememberLastUsedCamera: true,
                aspectRatio: 1.333,
                disableFlip: false,
                showTorchButtonIfSupported: true,
                experimentalFeatures: {
                    // ZXing fallback is often more reliable for mixed barcode symbologies across browsers.
                    useBarCodeDetectorIfSupported: false,
                },
            };

            const formats = getScannerFormats();
            if (formats) {
                config.formatsToSupport = formats;
            }

            return config;
        }

        async function initializeCameraScanner() {
            if (typeof Html5Qrcode === 'undefined') {
                showErrorMessage('Library scanner tidak termuat. Coba refresh halaman.');
                return;
            }

            try {
                html5QrScanner = new Html5Qrcode('reader');

                const cameras = await Html5Qrcode.getCameras();
                const rearRegex = /(rear|back|environment|traseira|tr\u00e1s)/i;
                const frontRegex = /(front|user|facetime|webcam|integrated)/i;
                const selectedCamera = cameras.find((camera) => rearRegex.test(camera.label || '')) || cameras[0] || null;

                if (selectedCamera) {
                    activeCameraIsFront = frontRegex.test(selectedCamera.label || '');
                    applyMirrorFix(activeCameraIsFront);
                }

                const config = getScannerConfig();

                await html5QrScanner.start(
                    selectedCamera ? selectedCamera.id : { facingMode: { exact: 'environment' } },
                    config,
                    (decodedText) => {
                        processScannedCode(decodedText);
                    },
                    () => {}
                );
            } catch (envError) {
                try {
                    if (!html5QrScanner) {
                        html5QrScanner = new Html5Qrcode('reader');
                    }

                    activeCameraIsFront = true;
                    applyMirrorFix(true);

                    await html5QrScanner.start(
                        { facingMode: 'user' },
                        getScannerConfig(),
                        (decodedText) => {
                            processScannedCode(decodedText);
                        },
                        () => {}
                    );
                } catch (fallbackError) {
                    setCameraErrorState();
                }
            }
        }

        function setCameraErrorState() {
            document.getElementById('cameraError').classList.remove('hidden');
            document.getElementById('focusBox').classList.add('hidden');
            statusDisplay.textContent = 'INPUT MANUAL';
            statusDisplay.style.color = '#818cf8';

            // Setup manual input
            if (manualNisnInput) {
                manualNisnInput.focus();

                if (!manualNisnInput.dataset.listenerBound) {
                    manualNisnInput.dataset.listenerBound = '1';
                    manualNisnInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        submitManualNisn();
                    }
                    });
                }
            }
        }

        function submitManualNisn() {
            const nisn = normalizeScannedValue(manualNisnInput ? manualNisnInput.value : '');
            if (!nisn) return;
            submitAttendance(nisn);
            if (manualNisnInput) {
                manualNisnInput.value = '';
                manualNisnInput.focus();
            }
        }

        async function retryCamera() {
            document.getElementById('cameraError').classList.add('hidden');
            document.getElementById('focusBox').classList.remove('hidden');
            statusDisplay.textContent = 'SIAP';
            statusDisplay.style.color = '#9ca3af';
            if (html5QrScanner) {
                try { await html5QrScanner.stop(); } catch(e) {}
            }
            html5QrScanner = null;
            initializeCameraScanner();
        }

        function applyMirrorFix(isFrontCamera) {
            if (!reader) return;

            if (isFrontCamera) {
                reader.classList.add('camera-unmirror');
            } else {
                reader.classList.remove('camera-unmirror');
            }
        }

        function processScannedCode(decodedText) {
            if (isProcessingScan) return;

            isProcessingScan = true;

            const normalized = normalizeScannedValue(decodedText);
            if (!normalized) {
                isProcessingScan = false;
                return;
            }

            if (isDuplicateRecentScan(normalized)) {
                isProcessingScan = false;
                return;
            }

            nisnInput.value = normalized;
            submitAttendance(normalized).finally(() => {
                setTimeout(() => {
                    isProcessingScan = false;
                }, 1200);
            });
        }

        function isDuplicateRecentScan(code) {
            const now = Date.now();
            const isDuplicate = code === lastSubmittedCode && (now - lastSubmittedAt) < DUPLICATE_SCAN_COOLDOWN_MS;

            if (!isDuplicate) {
                lastSubmittedCode = code;
                lastSubmittedAt = now;
            }

            return isDuplicate;
        }

        function normalizeScannedValue(rawValue) {
            const raw = (rawValue || '').toString().trim();
            if (!raw) {
                return '';
            }

            const decoded = decodeURIComponentSafe(raw);

            const labelMatch = decoded.match(/(?:NISN|QR(?:_?CODE)?|CODE)\s*[:=]\s*([A-Za-z0-9\-]+)/i);
            if (labelMatch && labelMatch[1]) {
                return labelMatch[1].trim();
            }

            if (/^https?:\/\//i.test(decoded)) {
                try {
                    const parsed = new URL(decoded);
                    const queryKeys = ['nisn', 'code', 'qr', 'qr_code', 'value'];
                    for (const key of queryKeys) {
                        const value = parsed.searchParams.get(key);
                        if (value && value.trim()) {
                            return value.trim();
                        }
                    }

                    const pathSegments = parsed.pathname.split('/').filter(Boolean);
                    for (let i = pathSegments.length - 1; i >= 0; i--) {
                        const segment = pathSegments[i].trim();
                        if (/^(SH-[A-Z0-9]{8}|\d{8,20})$/i.test(segment)) {
                            return segment;
                        }
                    }
                } catch (e) {
                    // ignore parse errors and fallback to raw value
                }
            }

            const qrPatternMatch = decoded.match(/SH-[A-Z0-9]{8}/i);
            if (qrPatternMatch && qrPatternMatch[0]) {
                return qrPatternMatch[0].toUpperCase();
            }

            return decoded;
        }

        function decodeURIComponentSafe(value) {
            try {
                return decodeURIComponent(value);
            } catch (e) {
                return value;
            }
        }

        function setupUsbScannerCapture() {
            document.addEventListener('keydown', (event) => {
                if (event.ctrlKey || event.metaKey || event.altKey || isProcessingScan) {
                    return;
                }

                if (event.target === manualNisnInput || event.target === nisnInput) {
                    return;
                }

                if (event.key === 'Enter') {
                    if (keyboardBuffer.length >= MIN_SCANNER_INPUT_LENGTH) {
                        event.preventDefault();
                        const scannedCode = keyboardBuffer;
                        keyboardBuffer = '';
                        processScannedCode(scannedCode);
                    }
                    return;
                }

                if (event.key.length === 1) {
                    keyboardBuffer += event.key;
                    if (keyboardBufferTimeout) {
                        clearTimeout(keyboardBufferTimeout);
                    }

                    keyboardBufferTimeout = setTimeout(() => {
                        keyboardBuffer = '';
                    }, KEYBOARD_BUFFER_RESET_MS);
                }
            }, true);
        }

        // Handle NISN input
        nisnInput.addEventListener('keypress', async (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const nisn = normalizeScannedValue(nisnInput.value);
                
                if (!nisn) return;
                await submitAttendance(nisn);
                nisnInput.value = '';
            }
        });

        async function submitAttendance(nisn) {
            try {
                const response = await fetch("{{ route('admin.attendance.scan') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify({ nisn: nisn })
                });

                const contentType = response.headers.get('content-type') || '';
                const isJson = contentType.includes('application/json');

                if (!isJson) {
                    const rawText = await response.text();
                    throw new Error('Server mengembalikan respons tidak valid (' + response.status + '). ' + rawText.substring(0, 120));
                }

                const data = await response.json();

                if (!response.ok) {
                    const fallbackMessage = data.message || 'Gagal menyimpan scan (HTTP ' + response.status + ').';
                    showErrorMessage(fallbackMessage);
                    return;
                }

                if (data.success) {
                    updateLatestScanned(data.data);
                    addToLog(data.data);
                    showSuccessAnimation();
                    if (manualNisnInput && !document.getElementById('cameraError').classList.contains('hidden')) {
                        manualNisnInput.value = '';
                        manualNisnInput.focus();
                    }
                } else {
                    showErrorMessage(data.message || 'Scan tidak dapat diproses.');
                }
            } catch (error) {
                showErrorMessage('Terjadi kesalahan: ' + error.message);
            }
        }

        function updateLatestScanned(data) {
            studentName.textContent = data.student_name || '-';
            studentClass.textContent = data.class_name || '-';
            const formattedCheckIn = formatTimeLabel(data.check_in);
            const formattedCheckOut = formatTimeLabel(data.check_out);
            const formattedTimestamp = formatTimeLabel(data.timestamp);
            checkInTime.textContent = formattedCheckIn || formattedCheckOut || formattedTimestamp || '--:--';
            const normalizedStatus = (data.status || '').toString().toLowerCase();
            
            const statusColor = {
                'present': '#10b981',
                'late': '#f59e0b',
                'absent': '#ef4444',
                'hadir': '#10b981',
                'telat': '#f59e0b',
                'alpa': '#ef4444',
                'izin': '#0ea5e9',
                'sakit': '#8b5cf6',
            };
            
            statusDisplay.textContent = (data.status || 'SIAP').toUpperCase();
            statusDisplay.style.color = statusColor[normalizedStatus] || '#9ca3af';
        }

        function formatTimeLabel(value) {
            if (typeof value !== 'string') {
                return '';
            }

            const normalized = value.trim();
            if (!normalized) {
                return '';
            }

            if (/^\d{2}:\d{2}:\d{2}$/.test(normalized)) {
                return normalized.substring(0, 5);
            }

            if (/^\d{2}:\d{2}$/.test(normalized)) {
                return normalized;
            }

            const parsed = new Date(normalized);
            if (Number.isNaN(parsed.getTime())) {
                return '';
            }

            return parsed.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
            });
        }

        function addToLog(data) {
            const normalizedStatus = (data.status || '').toString().toLowerCase();
            const statusIcon = {
                'present': 'check_circle',
                'late': 'schedule',
                'absent': 'cancel',
                'hadir': 'check_circle',
                'telat': 'schedule',
                'alpa': 'cancel',
                'izin': 'event_busy',
                'sakit': 'health_and_safety',
            };
            
            const statusColorClass = {
                'present': 'emerald',
                'late': 'amber',
                'absent': 'red',
                'hadir': 'emerald',
                'telat': 'amber',
                'alpa': 'red',
                'izin': 'sky',
                'sakit': 'violet',
            };

            const icon = statusIcon[normalizedStatus] || 'help';
            const colorClass = statusColorClass[normalizedStatus] || 'slate';

            const logEntry = document.createElement('div');
            logEntry.className = 'flex items-center gap-4 p-3 rounded-2xl hover:bg-white/5 transition-colors group';
            logEntry.innerHTML = `
                <div class="w-10 h-10 rounded-full bg-${colorClass}-500/10 flex items-center justify-center text-${colorClass}-400 shrink-0">
                    <span class="material-symbols-outlined text-lg">${icon}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">${data.student_name}</p>
                    <p class="text-[10px] font-medium text-slate-500">${data.timestamp} · ${data.class_name}</p>
                </div>
            `;

            if (scanLogs.querySelector('p')) {
                scanLogs.innerHTML = '';
            }

            scanLogs.insertAdjacentElement('afterbegin', logEntry);
        }

        function showSuccessAnimation() {
            const icon = document.getElementById('studentIcon');
            icon.textContent = 'check_circle';
            icon.style.color = '#10b981';
            
            setTimeout(() => {
                icon.textContent = 'person';
                icon.style.color = '#cbd5e1';
            }, 2000);

            nisnInput.focus();
        }

        function showErrorMessage(message) {
            showBannerError(message);
            nisnInput.focus();
        }

        function showBannerError(message) {
            const banner = document.getElementById('errorBanner');
            document.getElementById('errorBannerText').textContent = message;
            banner.classList.remove('hidden');
            setTimeout(() => banner.classList.add('hidden'), 5000);
        }

        function clearScannedData() {
            scanLogs.innerHTML = '<p class="text-center text-sm text-slate-400 py-8">Belum ada scan dalam sesi ini</p>';
            studentName.textContent = '-';
            studentClass.textContent = '-';
            checkInTime.textContent = '--:--';
            statusDisplay.textContent = 'SIAP';
            statusDisplay.style.color = '#9ca3af';
            nisnInput.value = '';
            nisnInput.focus();
        }
    </script>

    <style>
        /* Container */
        #reader {
            border: none !important;
            width: 100% !important;
            height: 100% !important;
            position: absolute !important;
            inset: 0 !important;
        }

        /* All inner wrappers must fill the space */
        #reader > div,
        #reader__scan_region {
            width: 100% !important;
            height: 100% !important;
            min-height: 100% !important;
        }

        #reader__scan_region {
            display: flex !important;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Hide library's dashboard UI (camera selector etc) */
        #reader__dashboard {
            display: none !important;
        }

        /* Hide the library's built-in scan region border (white corners) */
        #reader__scan_region > canvas,
        #reader__scan_region > img:not(video) {
            display: none !important;
        }

        /* The qr-shaded region drawn by html5-qrcode */
        #qr-shaded-region {
            display: none !important;
        }

        /* Force video to cover the entire container */
        #reader video,
        #reader__scan_region video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            position: absolute !important;
            inset: 0 !important;
        }

        /* Mirror fix for front camera */
        #reader.camera-unmirror video,
        #reader.camera-unmirror #reader__scan_region video {
            transform: scaleX(-1);
        }
    </style>
</body>
</html>
