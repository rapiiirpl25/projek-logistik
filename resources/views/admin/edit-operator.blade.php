<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data Kerja Personel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light pb-5">
    <div class="container my-5" style="max-width: 600px;">
        <div class="card border-0 shadow-sm p-4 rounded-3">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h4 class="fw-bold m-0 text-dark">Ubah Data Kerja Personel</h4>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary">Kembali</a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger p-2 small">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('admin.operator.update', $operator->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Username -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Lengkap (Username)</label>
                    <input type="text" name="username" class="form-control"
                        value="{{ old('username', $operator->username) }}" required>
                </div>

                <!-- Kode Akses -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Kode Akses Personel</label>
                    <input type="text" name="code" class="form-control font-monospace text-uppercase" maxlength="12"
                        value="{{ old('code', $operator->code) }}" required>
                </div>

                <!-- Email Tambahan -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Email Personel (Opsional)</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $operator->email) }} "value="{{ old('email', $operator->email) }}">
                    <div class="form-text small text-muted">Dapat dikosongkan jika personel belum memiliki email kerja.
                    </div>
                </div>

                <!-- Password Tambahan -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Password Akun (Opsional)</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password baru...">
                    <div class="form-text small text-muted">Biarkan kosong jika tidak ingin mengubah password yang sudah
                        ada.</div>
                </div>

                <!-- Pergantian Tingkatan / Hak Akses -->
                <div class="mb-4">
                    <label class="form-label fw-semibold small">Tingkatan / Hak Akses Kerja</label>
                    <select name="role" class="form-select" required>
                        <option value="operator" {{ old('role', $operator->role) === 'operator' ? 'selected' : '' }}>
                            Operator (Pelaksana Lapangan)
                        </option>
                        <option value="sub_leader" {{ old('role', $operator->role) === 'sub_leader' ? 'selected' : '' }}>
                            Sub Leader
                        </option>
                        <option value="leader" {{ old('role', $operator->role) === 'leader' ? 'selected' : '' }}>
                            Leader
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn btn-warning w-100 py-2 fw-semibold">Simpan Perubahan Data
                    Kerja</button>
            </form>
        </div>
    </div>
</body>

</html>