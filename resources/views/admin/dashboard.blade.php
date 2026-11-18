<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Manajemen Logistik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light pb-5">
    <nav class="navbar navbar-dark bg-dark px-4 py-2 shadow-sm">
        <span class="navbar-brand fw-bold">Logistik Manager</span>
        <div class="d-flex align-items-center gap-3">
            <span class="text-light small">{{ Auth::user()->username }} ({{ Auth::user()->code }})</span>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button class="btn btn-sm btn-outline-danger">Keluar</button>
            </form>
        </div>
    </nav>

    <div class="container-fluid my-4 px-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-3 col-lg-2 mb-4">
                <div class="card border-0 shadow-sm sidebar-dark">
                    <div class="card-body p-2">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">
                            <button class="nav-link active text-start" id="tab-monitoring" data-bs-toggle="pill"
                                data-bs-target="#content-monitoring" type="button" role="tab">
                                Menu Tugas
                            </button>
                            <button class="nav-link text-start" id="tab-inventory" data-bs-toggle="pill"
                                data-bs-target="#content-inventory" type="button" role="tab">
                                Menu Riwayat Box Retur
                            </button>
                            <button class="nav-link text-start" id="tab-personnel" data-bs-toggle="pill"
                                data-bs-target="#content-personnel" type="button" role="tab">
                                Menu Data Kerja Personel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9 col-lg-10">
                <div class="tab-content" id="v-pills-tabContent">

                    <div class="tab-pane fade show active" id="content-monitoring" role="tabpanel">

                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card p-3 shadow-sm border-0">
                                    <small class="text-muted">Total Tugas</small>
                                    <h3 class="fw-bold m-0">{{ $stats['total'] }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card p-3 shadow-sm border-0 border-start border-success border-4">
                                    <small class="text-muted">Selesai</small>
                                    <h3 class="fw-bold text-success m-0">{{ $stats['approved'] }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card p-3 shadow-sm border-0 border-start border-warning border-4">
                                    <small class="text-muted">Sedang Dikerjakan</small>
                                    <h3 class="fw-bold text-warning m-0">{{ $stats['pending'] }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card p-3 shadow-sm border-0 border-start border-info border-4">
                                    <small class="text-muted">Menunggu Persetujuan</small>
                                    <h3 class="fw-bold text-info m-0">{{ $stats['waiting'] }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0 text-dark">Manajemen Tugas</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('report.print') }}" target="_blank"
                                    class="btn btn-outline-secondary btn-sm">Cetak Laporan</a>
                                <a href="{{ route('admin.task.create') }}" class="btn btn-primary btn-sm">+ Buat Tugas
                                    Baru</a>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No. Tugas</th>
                                            <th>Rincian Tugas</th>
                                            <th>Operator</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Laporan / Kendala Operator</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tasks as $t)
                                            <tr>
                                                <td class="fw-bold text-primary">{{ $t->task_code }}</td>
                                                <td>
                                                    <strong>{{ $t->title }}</strong><br>
                                                    <small class="text-muted"
                                                        style="white-space: pre-line;">{{ $t->description }}</small>
                                                </td>
                                                <td>{{ $t->operator->username ?? '-' }}</td>
                                                <td>{{ $t->task_date }}</td>
                                                <td>
                                                    @if($t->status == 'pending') <span
                                                        class="badge bg-secondary">Tertunda</span>
                                                    @elseif($t->status == 'in_progress') <span
                                                        class="badge bg-primary">Dikerjakan</span>
                                                    @elseif($t->status == 'waiting_approval') <span
                                                        class="badge bg-warning text-dark">Perlu Ditinjau</span>
                                                    @elseif($t->status == 'approved') <span
                                                        class="badge bg-success">Disetujui</span>
                                                    @elseif($t->status == 'need_revision') <span
                                                        class="badge bg-danger">Revisi</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($t->operator_report)
                                                        <strong>Laporan:</strong> {{ $t->operator_report }}<br>
                                                        @if($t->issue_note)
                                                            <small class="text-danger">Kendala: {{ $t->issue_note }}</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted small">Belum ada laporan</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($t->status == 'waiting_approval')
                                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                            data-bs-target="#modalApprove{{ $t->id }}">Tinjau</button>

                                                        <div class="modal fade" id="modalApprove{{ $t->id }}" tabindex="-1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content text-start">
                                                                    <form action="{{ route('admin.task.review', $t->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Tinjau Laporan Tugas:
                                                                                {{ $t->task_code }}
                                                                            </h5>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p><strong>Laporan Operator:</strong>
                                                                                {{ $t->operator_report }}
                                                                            </p>
                                                                            @if($t->issue_note)
                                                                                <p class="text-danger"><strong>Kendala:</strong>
                                                                                    {{ $t->issue_note }}
                                                                                </p>
                                                                            @endif
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Catatan Feedback /
                                                                                    Revisi</label>
                                                                                <textarea name="admin_feedback"
                                                                                    class="form-control"
                                                                                    placeholder="Wajib diisi jika minta revisi">{{ $t->admin_feedback }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" name="action" value="revise"
                                                                                class="btn btn-warning">Revisi</button>
                                                                            <button type="submit" name="action" value="approve"
                                                                                class="btn btn-success">Setuju
                                                                                (Selesai)</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">Belum ada tugas
                                                    operasional terdaftar.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="content-inventory" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0 text-dark">Riwayat Box Retur</h5>
                        </div>
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Waktu Lapor</th>
                                                <th>Operator</th>
                                                <th>Jenis Box</th>
                                                <th>Kuantitas (Pcs)</th>
                                                <th>Keterangan / Kondisi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($incomingReports as $report)
                                                <tr>
                                                    <td class="small">
                                                        {{ \Carbon\Carbon::parse($report->created_at)->format('d M Y, H:i') }}
                                                    </td>
                                                    <td>
                                                        <span class="fw-semibold text-primary">
                                                            {{ $report->operator->username ?? 'Tidak Diketahui' }}
                                                        </span>
                                                    </td>
                                                    <td class="fw-semibold">{{ $report->part_name }}</td>
                                                    <td><span class="badge bg-success">{{ $report->quantity }} Pcs</span>
                                                    </td>
                                                    <td class="small text-muted">{{ $report->notes ?: '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat
                                                        laporan pemasukan box.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="content-personnel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0 text-dark">Manajemen Data Kerja Personel</h5>
                            <a href="{{ route('admin.operator.create') }}" class="btn btn-primary btn-sm">+ Tambah
                                Personel</a>
                        </div>
                        <div class="card shadow-sm border-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th>Nama Lengkap</th>
                                            <th>Kode Akses</th>
                                            <th>Email Kerja</th>
                                            <th>Tingkatan / Role</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($allOperators as $op)
                                            <tr>
                                                <td class="fw-semibold">{{ $op->username }}</td>
                                                <td><span class="badge bg-dark font-monospace">{{ $op->code }}</span></td>
                                                <td>{{ $op->email ?? '-' }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $op->role === 'leader' ? 'bg-danger' : ($op->role === 'sub_leader' ? 'bg-primary' : 'bg-success') }}">
                                                        {{ strtoupper(str_replace('_', ' ', $op->role)) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.operator.edit', $op->id) }}"
                                                        class="btn btn-sm btn-outline-warning fw-semibold">
                                                        Ubah Data Kerja
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-3 text-muted">Belum ada personel
                                                    terdaftar.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<style>
        .sidebar-dark {
            background-color: #212529;
            border-radius: 8px;
        }

        .nav-pills .nav-link {
            color: #ced4da;
            border-radius: 6px;
            margin-bottom: 5px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .nav-pills .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .nav-pills .nav-link.active {
            background-color: #0d6efd;
            color: #ffffff;
        }
    </style>

</html>