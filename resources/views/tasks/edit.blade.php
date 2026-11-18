<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tugas Logistik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container my-5" style="max-width: 650px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Edit Tugas Logistik</h3>
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Tugas / Resi</label>
                        <input type="text" name="task_code"
                            class="form-control @error('task_code') is-invalid @enderror"
                            value="{{ old('task_code', $task->task_code) }}" required>
                        @error('task_code')
                        <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">Nama Barang</label>
                            <input type="text" name="item_name"
                                class="form-control @error('item_name') is-invalid @enderror"
                                value="{{ old('item_name', $task->item_name) }}" required>
                            @error('item_name')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Jumlah</label>
                            <input type="number" name="quantity"
                                class="form-control @error('quantity') is-invalid @enderror"
                                value="{{ old('quantity', $task->quantity) }}" min="1" required>
                            @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Penerima</label>
                        <input type="text" name="recipient_name"
                            class="form-control @error('recipient_name') is-invalid @enderror"
                            value="{{ old('recipient_name', $task->recipient_name) }}" required>
                        @error('recipient_name')
                        <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Pengiriman</label>
                        <textarea name="destination_address" rows="3"
                            class="form-control @error('destination_address') is-invalid @enderror"
                            required>{{ old('destination_address', $task->destination_address) }}</textarea>
                        @error('destination_address')
                        <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nama Supir / Kurir</label>
                            <input type="text" name="driver_name" class="form-control"
                                value="{{ old('driver_name', $task->driver_name) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Status Pengiriman</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="in_transit" {{ $task->status == 'in_transit' ? 'selected' : '' }}>Dalam
                                    Perjalanan</option>
                                <option value="delivered" {{ $task->status == 'delivered' ? 'selected' : '' }}>Terkirim
                                </option>
                                <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>Batal
                                </option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 mt-3 fw-semibold">Perbarui Data</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>