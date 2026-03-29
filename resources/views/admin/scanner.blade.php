<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>ScanHadir - QR Scanner Console</title>
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
</head>
<body class="bg-slate-950 font-body text-white overflow-hidden">
    <!-- Top Bar -->
    <nav class="flex justify-between items-center w-full px-8 py-4 bg-slate-900/50 backdrop-blur-xl border-b border-white/5">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center hover:bg-white/10 transition-colors">
                <span class="material-symbols-outlined text-white">arrow_back</span>
            </a>
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
    <form id="scanForm" method="POST" action="{{ route('admin.attendance.scan') }}" style="display: none;">
        @csrf
        <input id="nisnInput" type="text" name="nisn" autofocus>
    </form>

    <main class="flex h-[calc(100vh-76px)] p-8 gap-8">
        <!-- Scanner Viewport -->
        <div class="flex-1 relative bg-black rounded-[40px] overflow-hidden border border-white/10 shadow-2xl flex items-center justify-center">
            <!-- Simulated Video Feed -->
            <div class="absolute inset-0 opacity-40 mix-blend-overlay grayscale">
                <div class="w-full h-full bg-gradient-to-br from-indigo-900/20 to-slate-900/20"></div>
            </div>

            <!-- Focus Box -->
            <div class="relative w-80 h-80 z-10">
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
            <div class="absolute bottom-10 flex items-center gap-4">
                <button type="button" onclick="document.getElementById('nisnInput').focus()" class="px-6 py-3 bg-white/10 backdrop-blur-xl rounded-full text-xs font-bold uppercase tracking-widest hover:bg-white/20 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">videocam</span> Focus
                </button>
                <button type="button" onclick="clearScannedData()" class="px-6 py-3 bg-white/10 backdrop-blur-xl rounded-full text-xs font-bold uppercase tracking-widest hover:bg-white/20 transition-all flex items-center gap-2">
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
        let scannedStudents = [];

        // Focus on page load
        window.addEventListener('load', () => {
            nisnInput.focus();
        });

        // Handle NISN input
        nisnInput.addEventListener('keypress', async (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const nisn = nisnInput.value.trim();
                
                if (!nisn) return;

                try {
                    const response = await fetch("{{ route('admin.attendance.scan') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        },
                        body: JSON.stringify({ nisn: nisn })
                    });

                    const data = await response.json();

                    if (data.success) {
                        updateLatestScanned(data.data);
                        addToLog(data.data);
                        showSuccessAnimation();
                    } else {
                        showErrorMessage(data.message);
                    }
                } catch (error) {
                    showErrorMessage('Terjadi kesalahan: ' + error.message);
                }

                nisnInput.value = '';
            }
        });

        function updateLatestScanned(data) {
            studentName.textContent = data.student_name || '-';
            studentClass.textContent = data.class_name || '-';
            checkInTime.textContent = data.check_in.substring(0, 5) || '--:--';
            
            const statusColor = {
                'present': '#10b981',
                'late': '#f59e0b',
                'absent': '#ef4444'
            };
            
            statusDisplay.textContent = data.status.toUpperCase();
            statusDisplay.style.color = statusColor[data.status] || '#9ca3af';
        }

        function addToLog(data) {
            const statusIcon = {
                'present': 'check_circle',
                'late': 'schedule',
                'absent': 'cancel'
            };
            
            const statusColorClass = {
                'present': 'emerald',
                'late': 'amber',
                'absent': 'red'
            };

            const icon = statusIcon[data.status] || 'help';
            const colorClass = statusColorClass[data.status] || 'slate';

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

            if (scanLogs.querySelector('p:contains("Belum ada")')) {
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
            alert(message);
            nisnInput.focus();
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
</body>
</html>
