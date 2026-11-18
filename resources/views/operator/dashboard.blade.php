<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Operator - Manajemen Tugas Logistik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light pb-5">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3 mb-4 shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold">Logistik Operator</span>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white small">{{ Auth::user()->username }} ({{ Auth::user()->code }})</span>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Notifikasi Flash -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show p-2 small" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close small" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show p-2 small" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close small" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Menu Aksi Cepat / Quick Actions -->
        <div class="mb-4">
            <a href="{{ route('operator.inventory.incoming') }}" class="btn btn-primary shadow-sm fw-semibold">
                <i class="bi bi-box-arrow-in-down me-1"></i> Lapor Pemasukan Box Retur
            </a>
        </div>

        <!-- Daftar Tugas Operasional Hari Ini -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h4 class="fw-bold m-0 text-dark">Daftar Tugas Hari Ini ({{ date('d-m-Y') }})</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Tugas</th>
                                <th>Nama tugas</th>
                                <th>Status</th>
                                <th class="text-center">Rincian & Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasksToday as $task)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $task->task_code }}</td>
                                    <td>{{ $task->title }}</td>
                                    <td>
                                        @if($task->status === 'pending')
                                            <span class="badge bg-secondary">Tertunda</span>
                                        @elseif($task->status === 'in_progress')
                                            <span class="badge bg-primary">Dikerjakan</span>
                                        @elseif($task->status === 'waiting_approval')
                                            <span class="badge bg-warning text-dark">Menunggu Tinjauan</span>
                                        @elseif($task->status === 'approved')
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($task->status === 'need_revision')
                                            <span class="badge bg-danger">Perlu Revisi</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <!-- Tombol Rincian SPK -->
                                            <button type="button" class="btn btn-sm btn-info text-white"
                                                data-bs-toggle="modal" data-bs-target="#detailModalToday{{ $task->id }}">
                                                <i class="bi bi-info-circle"></i> Rincian Tugas
                                            </button>

                                            <!-- Tombol Mulai Tugas -->
                                            @if($task->status === 'pending')
                                                <form action="{{ route('operator.task.start', $task->id) }}" method="POST"
                                                    class="m-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary">Mulai</button>
                                                </form>
                                            @endif

                                            <!-- Tombol Lapor Hasil / Revisi -->
                                            @if($task->status === 'in_progress' || $task->status === 'need_revision')
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#reportModalToday{{ $task->id }}">
                                                    Lapor
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Rincian SPK Hari Ini -->
                                <div class="modal fade" id="detailModalToday{{ $task->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light">
                                                <h6 class="modal-title fw-bold">Rincian Tugas:
                                                    {{ $task->task_code }}
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="mb-3">
                                                    <label class="fw-semibold small text-muted">Nama tugas</label>
                                                    <div class="fw-bold">{{ $task->title }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="fw-semibold small text-muted">Tanggal Penugasan</label>
                                                    <div>{{ \Carbon\Carbon::parse($task->task_date)->format('d F Y') }}
                                                    </div>
                                                </div>

                                                <!-- Panduan Standar WI -->
                                                <div class="p-2 border rounded bg-light small mb-3">
                                                    <span class="fw-semibold text-primary d-block mb-1">Standar Prosedur
                                                        (WI):</span>
                                                    @if($task->title === 'Prepare Empty Box')
                                                        Menyiapkan kembali box yang akan digunakan untuk kebutuhan produksi.
                                                    @elseif($task->title === 'Prepare Delivery Local')
                                                        1. Mengeluarkan barang dari rak penyimpanan.<br>
                                                        2. Memasang dan memberi stempel pada label setiap box.<br>
                                                        3. Strapping band muatan barang.<br>
                                                        4. Membungkus barang dengan plastic wrap.
                                                    @elseif($task->title === 'Prepare Delivery EXIM')
                                                        1. Mengeluarkan barang dari rak penyimpanan.<br>
                                                        2. Memasang stiker nomor per karton.<br>
                                                        3. Strapping band karton barang.<br>
                                                        4. Membungkus karton barang bagian atas dengan lembar plastik.<br>
                                                        5. Membungkus karton barang beserta siku dengan plastic wrap.
                                                    @else
                                                        Proses packing komponen sparepart sesuai standar proteksi packing part
                                                        presisi.
                                                    @endif
                                                </div>

                                                <div class="mb-3">
                                                    <label class="fw-semibold small text-muted">Instruksi Muatan &
                                                        Pengiriman (Outbound)</label>
                                                    <div class="bg-light p-3 rounded border small font-monospace">
                                                        @foreach(explode("\n", $task->description) as $line)
                                                            @php
                                                                $parts = explode(':', $line, 2);
                                                            @endphp
                                                            @if(count($parts) === 2)
                                                                <div class="mb-1">
                                                                    <strong class="text-dark">{{ trim($parts[0]) }}:</strong>
                                                                    <span>{{ $parts[1] }}</span>
                                                                </div>
                                                            @else
                                                                <div>{{ $line }}</div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>

                                                @if($task->admin_feedback)
                                                    <div class="alert alert-warning p-2 small mt-2">
                                                        <strong>Catatan Evaluasi / Revisi Leader:</strong><br>
                                                        {{ $task->admin_feedback }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer py-1">
                                                <button type="button" class="btn btn-sm btn-secondary"
                                                    data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Laporan Hari Ini -->
                                <div class="modal fade" id="reportModalToday{{ $task->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <form action="{{ route('operator.task.report', $task->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header bg-success text-white">
                                                    <h6 class="modal-title fw-bold">Lapor Hasil Tugas:
                                                        {{ $task->task_code }}
                                                    </h6>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small">Laporan Penyelesaian /
                                                            Hasil Muatan</label>
                                                        <textarea name="operator_report" class="form-control" rows="3"
                                                            placeholder="Contoh: Barang sudah selesai di-strapping dan wrap rapi..."
                                                            required>{{ $task->operator_report }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small text-danger">Catatan
                                                            Kendala Lapangan (Opsional)</label>
                                                        <textarea name="issue_note" class="form-control" rows="2"
                                                            placeholder="Contoh: Stok box kurang, stiker sobek, plastic wrap habis...">{{ $task->issue_note }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer py-1">
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-sm btn-success">Kirim ke
                                                        Leader</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada tugas operasional yang
                                        ditugaskan
                                        hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Riwayat Seluruh Tugas Operator -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h4 class="fw-bold m-0 text-dark">Daftar Riwayat Tugas</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Tugas</th>
                                <th>Nama tugas</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th class="text-center">Rincian & Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allTasks as $task)
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $task->task_code }}</td>
                                    <td>{{ $task->title }}</td>
                                    <td>{{ $task->task_date }}</td>
                                    <td>
                                        @if($task->status === 'pending')
                                            <span class="badge bg-secondary">Tertunda</span>
                                        @elseif($task->status === 'in_progress')
                                            <span class="badge bg-primary">Dikerjakan</span>
                                        @elseif($task->status === 'waiting_approval')
                                            <span class="badge bg-warning text-dark">Menunggu Tinjauan</span>
                                        @elseif($task->status === 'approved')
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($task->status === 'need_revision')
                                            <span class="badge bg-danger">Perlu Revisi</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <!-- Tombol Rincian -->
                                            <button type="button" class="btn btn-sm btn-outline-secondary py-0"
                                                data-bs-toggle="modal" data-bs-target="#detailModalAll{{ $task->id }}">
                                                Rincian Tugas
                                            </button>

                                            <!-- Tombol Mulai (jika status pending) -->
                                            @if($task->status === 'pending')
                                                <form action="{{ route('operator.task.start', $task->id) }}" method="POST"
                                                    class="m-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary py-0">Mulai</button>
                                                </form>
                                            @endif

                                            <!-- Tombol Lapor (jika in_progress atau need_revision) -->
                                            @if($task->status === 'in_progress' || $task->status === 'need_revision')
                                                <button type="button" class="btn btn-sm btn-success py-0" data-bs-toggle="modal"
                                                    data-bs-target="#reportModalAll{{ $task->id }}">
                                                    Lapor
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Rincian SPK All -->
                                <div class="modal fade" id="detailModalAll{{ $task->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light">
                                                <h6 class="modal-title fw-bold">Rincian Tugas: {{ $task->task_code }}
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="mb-3">
                                                    <label class="fw-semibold small text-muted">Nama tugas</label>
                                                    <div class="fw-bold">{{ $task->title }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="fw-semibold small text-muted">Tanggal Penugasan</label>
                                                    <div>{{ \Carbon\Carbon::parse($task->task_date)->format('d F Y') }}
                                                    </div>
                                                </div>

                                                <!-- Panduan Standar WI -->
                                                <div class="p-2 border rounded bg-light small mb-3">
                                                    <span class="fw-semibold text-primary d-block mb-1">Standar Prosedur
                                                        (WI):</span>
                                                    @if($task->title === 'Prepare Empty Box')
                                                        Menyiapkan kembali box yang akan digunakan untuk kebutuhan produksi.
                                                    @elseif($task->title === 'Prepare Delivery Local')
                                                        1. Mengeluarkan barang dari rak penyimpanan.<br>
                                                        2. Memasang dan memberi stempel pada label setiap box.<br>
                                                        3. Strapping band muatan barang.<br>
                                                        4. Membungkus barang dengan plastic wrap.
                                                    @elseif($task->title === 'Prepare Delivery EXIM')
                                                        1. Mengeluarkan barang dari rak penyimpanan.<br>
                                                        2. Memasang stiker nomor per karton.<br>
                                                        3. Strapping band karton barang.<br>
                                                        4. Membungkus karton barang bagian atas dengan lembar plastik.<br>
                                                        5. Membungkus karton barang beserta siku dengan plastic wrap.
                                                    @else
                                                        Proses packing komponen sparepart sesuai standar proteksi packing part
                                                        presisi.
                                                    @endif
                                                </div>

                                                <div class="mb-3">
                                                    <label class="fw-semibold small text-muted">Instruksi Muatan &
                                                        Pengiriman (Outbound)</label>
                                                    <div class="bg-light p-3 rounded border small font-monospace">
                                                        @foreach(explode("\n", $task->description) as $line)
                                                            @php
                                                                $parts = explode(':', $line, 2);
                                                            @endphp
                                                            @if(count($parts) === 2)
                                                                <div class="mb-1">
                                                                    <strong class="text-dark">{{ trim($parts[0]) }}:</strong>
                                                                    <span>{{ $parts[1] }}</span>
                                                                </div>
                                                            @else
                                                                <div>{{ $line }}</div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>

                                                @if($task->admin_feedback)
                                                    <div class="alert alert-warning p-2 small mt-2">
                                                        <strong>Catatan Evaluasi / Revisi Leader:</strong><br>
                                                        {{ $task->admin_feedback }}
                                                    </div>
                                                @endif
                                            </div>  
                                            <div class="modal-footer py-1">
                                                <button type="button" class="btn btn-sm btn-secondary"
                                                    data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Lapor All -->
                                <div class="modal fade" id="reportModalAll{{ $task->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <form action="{{ route('operator.task.report', $task->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header bg-success text-white">
                                                    <h6 class="modal-title fw-bold">Lapor Tugas: {{ $task->task_code }}</h6>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small">Laporan
                                                            Tugas</label>
                                                        <textarea name="operator_report" class="form-control" rows="3"
                                                            required>{{ $task->operator_report }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small text-danger">Kendala
                                                            (Opsional)</label>
                                                        <textarea name="issue_note" class="form-control"
                                                            rows="2">{{ $task->issue_note }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer py-1">
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-sm btn-success">Kirim</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">Belum ada riwayat tugas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>