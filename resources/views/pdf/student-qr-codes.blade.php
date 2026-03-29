<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Student QR Codes</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 18px;
        }

        .subtitle {
            margin: 0 0 18px;
            color: #4b5563;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
        }

        .card {
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }

        .box {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px;
        }

        .name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .meta {
            margin-bottom: 8px;
            color: #374151;
        }

        .qr {
            width: 140px;
            height: 140px;
            display: block;
            margin-top: 6px;
        }
    </style>
</head>
<body>
    <h1>Daftar QR Code Siswa</h1>
    <p class="subtitle">Generated: {{ now()->format('d M Y H:i') }}</p>

    <table class="grid">
        <tbody>
        @foreach($students as $student)
            @if($loop->index % 2 === 0)
                <tr>
            @endif

            <td class="card">
                <div class="box">
                    <div class="name">{{ $student['name'] }}</div>
                    <div class="meta">NISN: {{ $student['nisn'] }}</div>
                    <div class="meta">Kelas: {{ $student['class_name'] }}</div>
                    <div class="meta">Kode: {{ $student['qr_code'] }}</div>
                    <img class="qr" src="{{ $student['image'] }}" alt="QR {{ $student['name'] }}">
                </div>
            </td>

            @if($loop->index % 2 === 1 || $loop->last)
                @if($loop->index % 2 === 0)
                    <td class="card"></td>
                @endif
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</body>
</html>
