<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir Mahasiswa - {{ $jadwal->mata_kuliah }}</title>
    <style>
        /* Pengaturan Kertas & Font */
        @page { size: A4 landscape; margin: 25px 30px; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #000; }
        
        /* Utility CSS DomPDF */
        .w-full { width: 100%; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .border-collapse { border-collapse: collapse; }
        
        /* Kop Surat */
        .kop-surat { border-bottom: 1px solid #000; margin-bottom: 15px; padding-bottom: 10px; }
        .kop-text { line-height: 1.3; font-size: 11px; }
        .kop-text .univ { font-size: 13px; font-weight: bold; }
        .kop-text .fakultas { font-size: 14px; font-weight: bold; }

        /* Metadata */
        .metadata-area { margin-bottom: 15px; width: 100%; }
        .box-judul { border: 1px solid #000; font-size: 14px; font-weight: bold; text-align: center; padding: 10px; line-height: 1.4; width: 18%; vertical-align: middle; }
        .metadata-table { width: 100%; font-size: 11px; }
        .metadata-table td { padding: 2px 4px; vertical-align: top; font-weight: bold; }

        /* Tabel Utama */
        .table-absen { width: 100%; border-collapse: collapse; font-size: 9px; }
        .table-absen th, .table-absen td { border: 1px solid #000; padding: 4px; text-align: center; vertical-align: middle; }
        .table-absen th { background-color: #f9f9f9; font-weight: bold; }
        .table-absen td.text-left { text-align: left; padding-left: 5px; }
        
        /* TTD Area */
        .ttd-area { margin-top: 20px; width: 100%; font-size: 11px; }
    </style>
</head>
<body>

    @php
        $imagePath = public_path('images/logo.png');
        $logoBase64 = '';
        if (file_exists($imagePath)) {
            $logoData = base64_encode(file_get_contents($imagePath));
            $logoBase64 = 'data:image/png;base64,' . $logoData;
        }
    @endphp

    <table class="w-full kop-surat">
        <tr>
            <td width="15%" class="text-center">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" width="90" alt="Logo">
                @else
                    <h1 style="font-size:20px; margin:0;">LOGO</h1>
                @endif
            </td>
            <td width="85%" class="text-center kop-text">
                KEMENTERIAN PENDIDIKAN TINGGI, SAINS, dan TEKNOLOGI<br>
                <span class="univ">UNIVERSITAS MALIKUSSALEH</span><br>
                <span class="fakultas">FAKULTAS TEKNIK</span><br>
                Jalan Batam, Blang Pulo, Muara Satu - Lhokseumawe - Aceh (24352)<br>
                Telepon 0645-41373-40915 Faks 0645-44450<br>
                Laman: http://teknik.unimal.ac.id Email : ft@unimal.ac.id
            </td>
        </tr>
    </table>

    <table class="metadata-area">
        <tr>
            <td class="box-judul">
                DAFTAR<br>HADIR<br>MAHASISWA
            </td>
            <td width="2%"></td>
            
            <td width="40%" style="vertical-align: top;">
                <table class="metadata-table">
                    <tr><td width="30%">Mata Kuliah</td><td width="3%">:</td><td>{{ strtoupper($jadwal->mata_kuliah) }}</td></tr>
                    <tr><td>Kode</td><td>:</td><td>{{ $jadwal->kode_matkul }}</td></tr>
                    <tr><td>Kredit</td><td>:</td><td>{{ $jadwal->sks }}</td></tr>
                    <tr><td>Hari / Jam Ke</td><td>:</td><td>{{ $jadwal->hari }} / {{ substr($jadwal->jam_mulai, 0, 5) }}-{{ substr($jadwal->jam_selesai, 0, 5) }} WIB</td></tr>
                    <tr><td>Dosen Pengasuh</td><td>:</td><td>{{ strtoupper($jadwal->dosen->name ?? 'TIDAK DIKETAHUI') }}</td></tr>
                </table>
            </td>

            <td width="40%" style="vertical-align: top;">
                <table class="metadata-table">
                    <tr><td width="35%">Tahun Ajaran</td><td width="3%">:</td><td>{{ $jadwal->semester }}</td></tr>
                    <tr><td>Fakultas</td><td>:</td><td>TEKNIK</td></tr>
                    <tr><td>Program Studi</td><td>:</td><td>TEKNIK INFORMATIKA - S1 Reguler</td></tr>
                    <tr><td>Ruang</td><td>:</td><td>{{ strtoupper($jadwal->ruangan) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="table-absen">
        <thead>
            <tr>
                <th rowspan="3" width="2%">No.</th>
                <th rowspan="3" width="8%">NIM</th>
                <th rowspan="3" width="18%">Nama</th>
                <th colspan="16">Perkuliahan Ke / Tanggal</th>
                <th colspan="3">Rekapitulasi</th>
                <th rowspan="3" width="3%">Ket</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= 16; $i++)
                    <th width="3%">{{ $i }}</th>
                @endfor
                <th rowspan="2" width="3%">Target</th>
                <th rowspan="2" width="3%">Hadir</th>
                <th rowspan="2" width="3%">%</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= 16; $i++)
                    <th>
                        @if(isset($pertemuan[$i]))
                            {!! str_replace('-', '<br>', $pertemuan[$i]) !!}
                        @else
                            -
                        @endif
                    </th>
                @endfor
            </tr>
        </thead>
        <tbody>
            
            @forelse ($peserta as $index => $mhs)
                @php
                    $totalHadir = 0;
                    $target = 16;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $mhs->nim_nidn }}</td>
                    <td class="text-left">{{ strtoupper($mhs->name) }}</td>
                    
                    @for ($i = 1; $i <= 16; $i++)
                        @php
                            // Mengambil status absen (H/A/I/S) dari array/object data
                            $status = $mhs->absensi[$i] ?? ''; 
                            if($status == 'H') $totalHadir++;
                        @endphp
                        <td>{{ $status }}</td>
                    @endfor
                    
                    <td>{{ $target }}</td>
                    <td>{{ $totalHadir }}</td>
                    <td>{{ $target > 0 ? round(($totalHadir / $target) * 100, 2) : 0 }}</td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="23" style="padding: 15px;">Belum ada data mahasiswa terdaftar di kelas ini.</td>
                </tr>
            @endforelse
            
            <tr class="font-bold">
                <td>{{ count($peserta) + 1 }}</td>
                <td colspan="2" class="text-left">{{ $jadwal->dosen->nim_nidn ?? '-' }} | {{ strtoupper($jadwal->dosen->name ?? 'DOSEN') }}</td>
                
                @for ($i = 1; $i <= 16; $i++) 
                    <td>H</td> 
                @endfor
                
                <td>16</td>
                <td>16</td>
                <td>100</td>
                <td></td>
            </tr>

            <tr>
                <td colspan="3" class="text-left font-bold">Jumlah Mahasiswa Hadir</td>
                @for ($i = 1; $i <= 16; $i++) <td></td> @endfor
                <td colspan="4" style="background-color: #e5e5e5;"></td>
            </tr>
            <tr>
                <td colspan="3" class="text-left font-bold">Paraf Dosen</td>
                @for ($i = 1; $i <= 16; $i++) <td></td> @endfor
                <td colspan="4" style="background-color: #e5e5e5;"></td>
            </tr>
        </tbody>
    </table>

    <table class="ttd-area">
        <tr>
            <td width="70%"></td>
            
            <td width="30%" class="text-center">
                Mengetahui<br>
                Ketua Program Studi
                <br><br><br><br><br>
                <u>{{ $kaprodi_nama ?? 'Said Fadlan Anshari, S.Kom., M.Kom' }}</u><br>
                NIP. {{ $kaprodi_nip ?? '199402132022031011' }}
            </td>
        </tr>
    </table>

</body>
</html>