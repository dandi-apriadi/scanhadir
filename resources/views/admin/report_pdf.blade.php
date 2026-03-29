<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Laporan Presensi - ScanHadir</title>
    <style>
        @page {
            margin: 2cm;
            size: A4;
        }
        body {
            font-family: 'Inter', 'Helvetica', 'Arial', sans-serif;
            color: #1a202c;
            line-height: 1.5;
            font-size: 11pt;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 3pt double #2d3748;
            padding-bottom: 20pt;
            margin-bottom: 20pt;
        }
        .header h1 {
            margin: 0;
            font-size: 18pt;
            text-transform: uppercase;
            letter-spacing: 2pt;
        }
        .header p {
            margin: 5pt 0 0;
            font-size: 10pt;
            color: #718096;
        }
        .meta {
            margin-bottom: 20pt;
            display: flex;
            justify-content: space-between;
        }
        .meta-group {
            width: 48%;
        }
        .meta-label {
            font-size: 8pt;
            font-weight: bold;
            color: #a0aec0;
            text-transform: uppercase;
            letter-spacing: 1pt;
            margin-bottom: 2pt;
        }
        .meta-info {
            font-size: 11pt;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30pt;
        }
        th {
            background-color: #f7fafc;
            border-bottom: 2pt solid #edf2f7;
            padding: 10pt 8pt;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
        }
        td {
            padding: 8pt;
            border-bottom: 1pt solid #edf2f7;
            font-size: 10pt;
        }
        .status {
            font-weight: bold;
            font-size: 8pt;
            padding: 2pt 6pt;
            border-radius: 4pt;
            text-transform: uppercase;
        }
        .status-hadir { color: #2f855a; background-color: #f0fff4; }
        .status-terlambat { color: #c05621; background-color: #fffaf0; }
        .status-alpa { color: #c53030; background-color: #fff5f5; }
        
        .footer {
            margin-top: 50pt;
            text-align: right;
        }
        .footer-sign {
            margin-top: 60pt;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK Negeri 1 Bandung</h1>
        <p>Jl. Wastukencana No.75, Tamansari, Kec. Bandung Wetan, Kota Bandung, Jawa Barat 40116</p>
        <p>Laporan Presensi Siswa Terpadu</p>
    </div>

    <div class="meta">
        <div class="meta-group">
            <div class="meta-label">Mata Pelajaran</div>
            <div class="meta-info">Pemrograman Web & Mobile</div>
        </div>
        <div class="meta-group" style="text-align: right;">
            <div class="meta-label">Periode Laporan</div>
            <div class="meta-info">Senin, 29 Maret 2026</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30%">Nama Siswa</th>
                <th width="15%">NISN</th>
                <th width="15%">Kelas</th>
                <th width="15%">Waktu Masuk</th>
                <th width="25%">Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Ahmad Fauzi</strong></td>
                <td>0012345678</td>
                <td>XII RPL 1</td>
                <td>07:12:45</td>
                <td><span class="status status-hadir">Hadir</span></td>
            </tr>
            <tr>
                <td><strong>Siti Aminah</strong></td>
                <td>0012345679</td>
                <td>XII RPL 1</td>
                <td>07:35:12</td>
                <td><span class="status status-terlambat">Terlambat</span></td>
            </tr>
            <tr>
                <td><strong>Budi Santoso</strong></td>
                <td>0012345680</td>
                <td>XII RPL 1</td>
                <td>-</td>
                <td><span class="status status-alpa">Alpa</span></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Bandung, 29 Maret 2026</p>
        <p>Kepala Program Keahlian,</p>
        <div class="footer-sign">
            Indra Wijaya, S.Kom, M.T
            <br>
            <span style="font-weight: normal; font-size: 9pt;">NIP. 198501012010011001</span>
        </div>
    </div>
</body>
</html>
