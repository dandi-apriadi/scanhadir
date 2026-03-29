<!DOCTYPE html>
<html>
<head>
    <title>Laporan Presensi - ScanHadir</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #6366f1; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #6366f1; font-size: 24px; }
        .header p { margin: 5px 0 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f8fafc; color: #475569; padding: 10px; text-align: left; border: 1px solid #e2e8f0; }
        td { padding: 10px; border: 1px solid #e2e8f0; }
        .status-present { color: #10b981; font-weight: bold; }
        .status-late { color: #f59e0b; font-weight: bold; }
        .status-absent { color: #ef4444; font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ScanHadir</h1>
        <p>Laporan Kehadiran Siswa</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $attendance)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $attendance->date }}</td>
                <td>{{ $attendance->student->user->name }}</td>
                <td>{{ $attendance->student->class->name }}</td>
                <td>{{ $attendance->check_in ?? '-' }}</td>
                <td>{{ $attendance->check_out ?? '-' }}</td>
                <td class="status-{{ $attendance->status }}">
                    {{ ucfirst($attendance->status) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dihasilkan secara otomatis oleh Sistem ScanHadir.
    </div>
</body>
</html>
