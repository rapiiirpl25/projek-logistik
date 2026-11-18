<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Operasional Logistik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">
    <div class="container-fluid">

        {{-- ===== FILTER PENCARIAN (Disembunyikan saat dicetak) ===== --}}
        <div class="no-print mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold m-0 text-dark">Filter Pencarian Laporan</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('report.print') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-muted">Dari Tanggal</label>
                                <input type="date" class="form-control" name="start_date"
                                    value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-muted">Sampai Tanggal</label>
                                <input type="date" class="form-control" name="end_date"
                                    value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100 fw-semibold">Tampilkan Data</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===== AREA DOKUMEN LAPORAN ===== --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                {{-- Header Laporan --}}
                <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark">LAPORAN TUGAS OPERASIONAL LOGISTIK</h4>
                        <div class="small text-muted mb-2">
                            Dicetak oleh: {{ $user->username }} ({{ strtoupper($user->role) }}) |
                            Tanggal Cetak: {{ date('d-m-Y H:i') }}
                        </div>

                        {{-- Keterangan Rentang Waktu --}}
                        @if(request('start_date') && request('end_date'))
                            <span class="badge bg-light text-dark border">
                                Periode: {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} s/d
                                {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                            </span>
                        @elseif(request('start_date'))
                            <span class="badge bg-light text-dark border">
                                Periode: Mulai {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}
                            </span>
                        @elseif(request('end_date'))
                            <span class="badge bg-light text-dark border">
                                Periode: Sampai {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="badge bg-light text-dark border">Periode: Semua Waktu</span>
                        @endif
                    </div>

                    <div class="no-print">
                        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm fw-semibold">
                            Cetak Laporan
                        </button>
                    </div>
                </div>

                {{-- Tabel Data --}}
                @if ($tasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%" class="text-center">No</th>
                                    <th>No. Tugas</th>
                                    <th>Rincian Tugas</th>
                                    <th>Tanggal</th>
                                    <th>Operator</th>
                                    <th>Status</th>
                                    <th>Laporan / Kendala</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $index => $t)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="fw-bold text-primary">{{ $t->task_code }}</td>
                                        <td>
                                            <strong>{{ $t->title }}</strong><br>
                                            <span class="small text-muted">{{ $t->description }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($t->task_date)->format('d-m-Y') }}</td>
                                        <td>{{ $t->operator->username ?? '-' }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'secondary',
                                                    'in_progress' => 'primary',
                                                    'waiting_approval' => 'warning text-dark',
                                                    'approved' => 'success',
                                                    'need_revision' => 'danger',
                                                ];
                                                $statusLabels = [
                                                    'pending' => 'Menunggu',
                                                    'in_progress' => 'Dikerjakan',
                                                    'waiting_approval' => 'Menunggu Review',
                                                    'approved' => 'Selesai',
                                                    'need_revision' => 'Revisi',
                                                ];
                                                $color = $statusColors[$t->status] ?? 'secondary';
                                                $label = $statusLabels[$t->status] ?? strtoupper($t->status);
                                            @endphp
                                            <span class="badge bg-{{ $color }}">{{ $label }}</span>
                                        </td>
                                        <td>
                                            <span class="small d-block">{{ $t->operator_report ?? '-' }}</span>
                                            @if ($t->issue_note)
                                                <span class="small text-danger fw-semibold mt-1 d-block">Kendala:
                                                    {{ $t->issue_note }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-3 text-muted small">
                        Total Penugasan: <strong>{{ $tasks->count() }} Data</strong>
                    </div>
                @else
                    <div class="text-center py-5">
                        <h6 class="text-muted fw-bold">Tidak ada data ditemukan</h6>
                        <span class="small text-muted">Silakan sesuaikan rentang tanggal pada filter pencarian.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>

<style>
        /* Hanya CSS wajib untuk menyembunyikan tombol saat dicetak ke kertas/PDF */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background-color: white !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>

</html>