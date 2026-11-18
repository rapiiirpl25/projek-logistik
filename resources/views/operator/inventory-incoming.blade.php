<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemasukan Box Kosong</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light pb-5">
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0 text-dark">Lapor Pemasukan Box Retur</h4>
            <a href="{{ route('operator.dashboard') }}" class="btn btn-sm btn-secondary">Kembali ke Dasbor</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success p-3 small">{{ session('success') }}</div>
        @endif

        <div class="row g-4">
            <!-- Kolom Kiri: Form Input -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm p-4 rounded-3">
                    <h6 class="fw-bold text-primary mb-3">Buat Laporan Baru</h6>
                    <form action="{{ route('operator.inventory.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Jenis Box</label>
                            <select id="part_preset" class="form-select" required onchange="applyPartPreset()">
                                <option value="" disabled selected>-- Pilih Jenis Box --</option>
                                <option value="Box Hijau Kecil">Box Hijau Kecil</option>
                                <option value="Box Hijau Besar">Box Hijau Besar</option>
                                <option value="Box Biru">Box Biru</option>
                                <option value="MANUAL">Lainnya (Isi Manual)</option>
                            </select>
                        </div>

                        <!-- Input untuk Ketik Manual -->
                        <div class="mb-3" id="manual_input_area" style="display: none;">
                            <label class="form-label fw-semibold small">Ketik Jenis Box</label>
                            <input type="text" id="part_name" class="form-control mb-2"
                                placeholder="Contoh: Karton Box Standar">
                        </div>

                        <!-- Hidden input untuk mengirim part_name saat pilih dari dropdown -->
                        <input type="hidden" id="hidden_part_name" name="part_name">

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Kuantitas Masuk (Pcs)</label>
                            <input type="number" name="quantity" class="form-control" min="1" required
                                placeholder="Contoh: 50">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Keterangan / Kondisi (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="2"
                                placeholder="Contoh: Diterima dari truk retur MMKI, 2 box perlu dibersihkan ekstra."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-semibold">Kirim Laporan</button>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Riwayat Hari Ini -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm p-4 rounded-3 h-100">
                    <h6 class="fw-bold text-success mb-3">Riwayat Pemasukan Hari Ini ({{ date('d M Y') }})</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>Jenis Box</th>
                                    <th>Kuantitas (Pcs)</th>
                                    <th>Keterangan / Kondisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todayReports as $report)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($report->created_at)->format('H:i') }} WIB</td>
                                        <td class="fw-semibold">{{ $report->part_name }}</td>
                                        <td><span class="badge bg-success">{{ $report->quantity }} Pcs</span></td>
                                        <td class="text-muted">{{ $report->notes ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada laporan pemasukan box
                                            hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function applyPartPreset() {
            const select = document.getElementById('part_preset');
            const selectedOption = select.options[select.selectedIndex];
            const hiddenPartName = document.getElementById('hidden_part_name');

            const manualArea = document.getElementById('manual_input_area');
            const manualPartName = document.getElementById('part_name');

            if (selectedOption.value === "MANUAL") {
                // Mode Manual
                manualArea.style.display = 'block';
                manualPartName.required = true;
                manualPartName.name = "part_name";
                hiddenPartName.name = "";
            } else {
                // Mode Otomatis
                manualArea.style.display = 'none';
                manualPartName.required = false;
                manualPartName.name = "";
                hiddenPartName.name = "part_name";
                hiddenPartName.value = selectedOption.value;
            }
        }
    </script>
</body>

</html>