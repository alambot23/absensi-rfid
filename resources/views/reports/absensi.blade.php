<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">LAPORAN KEHADIRAN MAHASISWA</div>
        <div>Universitas Malikussaleh - Semester {{ $jadwal->semester }}</div>
    </div>

    <div>
        <p><strong>Mata Kuliah:</strong> {{ $jadwal->mata_kuliah }} ({{ $jadwal->kode_matkul }})</p>
        <p><strong>Dosen Pengampu:</strong> {{ $jadwal->dosen->name ?? '-' }}</p>
        <p><strong>Ruangan:</strong> {{ $jadwal->ruangan }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Waktu Tap</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absensi as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->mahasiswa->nim_nidn }}</td>
                <td>{{ $row->mahasiswa->name }}</td>
                <td>{{ $row->waktu_tap }}</td>
                <td>{{ strtoupper($row->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Ringkasan:</strong></p>
        <ul>
            <li>Total Mahasiswa: {{ $absensi->count() }}</li>
            <li>Hadir: {{ $absensi->where('status', 'hadir')->count() }}</li>
            <li>Sakit/Izin: {{ $absensi->whereIn('status', ['sakit', 'izin'])->count() }}</li>
            <li>Alpa: {{ $absensi->where('status', 'alpa')->count() }}</li>
        </ul>
    </div>

    <div style="margin-top: 50px;">
        <div style="float: right; width: 200px; text-align: center;">
            <p>Medan, {{ date('d-m-Y') }}</p>
            <p>Dosen Pengampu,</p>
            <br><br><br>
            <p><strong>{{ $jadwal->dosen->name ?? '................' }}</strong></p>
        </div>
    </div>
</body>
</html> 