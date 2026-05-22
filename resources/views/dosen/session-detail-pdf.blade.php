<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Sesi Presensi</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 5px; }
        h2 { font-size: 14px; margin-bottom: 3px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; font-size: 11px; }
        .info { margin-bottom: 15px; }
        .info p { margin: 3px 0; }
        .summary { margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap; }
        .summary-item { background: #f9f9f9; padding: 8px 12px; border-radius: 4px; }
        .status-hadir { color: #059669; font-weight: bold; }
        .status-telat { color: #d97706; font-weight: bold; }
        .status-sakit { color: #2563eb; font-weight: bold; }
        .status-izin { color: #4f46e5; font-weight: bold; }
        .status-alpa { color: #dc2626; font-weight: bold; }
        .status-pending { color: #6b7280; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Detail Sesi Presensi</h1>
    <div class="info">
        <p><strong>Tanggal:</strong> {{ $selectedDate }}</p>
        <p><strong>Mata Kuliah:</strong> {{ $subject->name }} ({{ $subject->code }})</p>
        <p><strong>Kelas:</strong> {{ $class->name }}</p>
    </div>

    <div class="summary">
        <div class="summary-item"><strong>Total:</strong> {{ $summary['total_students'] }}</div>
        <div class="summary-item"><span class="status-hadir">Hadir:</span> {{ $summary['hadir'] }}</div>
        <div class="summary-item"><span class="status-telat">Telat:</span> {{ $summary['telat'] }}</div>
        <div class="summary-item"><span class="status-sakit">Sakit:</span> {{ $summary['sakit'] }}</div>
        <div class="summary-item"><span class="status-izin">Izin:</span> {{ $summary['izin'] }}</div>
        <div class="summary-item"><span class="status-alpa">Alpa:</span> {{ $summary['alpa'] }}</div>
        <div class="summary-item"><span class="status-pending">Pending:</span> {{ $summary['pending'] }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama Siswa</th>
                <th>Status</th>
                <th>Metode</th>
                <th>Waktu Tap</th>
            </tr>
        </thead>
        <tbody>
            @forelse($studentRows as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['nisn'] }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td class="status-{{ strtolower($row['status']) }}">{{ $row['status'] === 'Pending' ? 'Belum Absensi' : $row['status'] }}</td>
                    <td>{{ $row['metode'] }}</td>
                    <td>{{ $row['waktu_tap'] }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align: center;">Tidak ada data siswa.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
