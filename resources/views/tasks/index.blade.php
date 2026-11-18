<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Logistik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <div class="container my-5">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">🚚 Dashboard Logistik</h2>
                <small class="text-muted">Kelola pengiriman dan penugasan kurir secara efisien</small>
            </div>
            <a href="{{ route('tasks.create') }}" class="btn btn-primary fw-semibold shadow-sm">+ Tambah Pengiriman</a>
        </div>

        <!-- Alert Notifikasi -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Card Statistik Dashboard -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <span class="text-muted fw-semibold">Total Tugas</span>
                        <h3 class="fw-bold my-1 text-dark">{{ $totalTasks }}</h3>
                        <small class="text-secondary">Semua pesanan terdaftar</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 border-start border-warning border-4">
                    <div class="card-body">
                        <span class="text-muted fw-semibold">Menunggu Diproses</span>
                        <h3 class="fw-bold my-1 text-warning">{{ $pendingTasks }}</h3>
                        <small class="text-secondary">Status Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 border-start border-info border-4">
                    <div class="card-body">
                        <span class="text-muted fw-semibold">Dalam Pengiriman</span>
                        <h3 class="fw-bold my-1 text-info">{{ $inTransitTasks }}</h3>
                        <small class="text-secondary">Sedang dibawa kurir</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 border-start border-success border-4">
                    <div class="card-body">
                        <span class="text-muted fw-semibold">Terkirim Sukses</span>
                        <h3 class="fw-bold my-1 text-success">{{ $deliveredTasks }}</h3>
                        <small class="text-secondary">Paket telah diterima</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="card border-0 shadow-sm mb-4 rounded-3">
            <div class="card-body p-3">
                <form action="{{ route('tasks.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control"
                            placeholder="Cari Kode Resi, Nama Barang, Penerima, atau Kurir..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>Dalam
                                Perjalanan</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Terkirim
                            </option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Batal
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100 fw-semibold">Filter</button>
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3">Kode Resi</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Penerima</th>
                                <th>Alamat Tujuan</th>
                                <th>Kurir</th>
                                <th>Status</th>
                                <th class="text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                <tr>
                                    <td class="ps-3 fw-bold text-primary">{{ $task->task_code }}</td>
                                    <td class="fw-semibold">{{ $task->item_name }}</td>
                                    <td>{{ $task->quantity }}</td>
                                    <td>{{ $task->recipient_name }}</td>
                                    <td class="text-truncate" style="max-width: 200px;"
                                        title="{{ $task->destination_address }}">{{ $task->destination_address }}</td>
                                    <td>{{ $task->driver_name ?? '-' }}</td>
                                    <td>
                                        @if($task->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($task->status == 'in_transit')
                                            <span class="badge bg-info text-dark">Dalam Perjalanan</span>
                                        @elseif($task->status == 'delivered')
                                            <span class="badge bg-success">Terkirim</span>
                                        @else
                                            <span class="badge bg-danger">Batal</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('tasks.edit', $task->id) }}"
                                                class="btn btn-sm btn-outline-warning">Edit</a>
                                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus tugas logistik ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Data tugas logistik tidak ditemukan.
                                    </td>
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