<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Operator Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light pb-5">
    <div class="container my-5" style="max-width: 600px;">
        <div class="card border-0 shadow-sm p-4 rounded-3">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h4 class="fw-bold m-0 text-dark">Tambah Operator Baru</h4>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary">Kembali</a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger p-2 small">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('admin.operator.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Lengkap (Username)</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan Username Anda"
                        value="{{ old('username') }}" autocomplete="off" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Kode Akses Personel</label>
                    <input type="text" name="code" class="form-control"
                        placeholder="Masukkan Kode Akses Anda" maxlength="12" value="{{ old('code') }}" required>
                    <div class="form-text small text-muted">
                        Format: [Role: OP/L/SL][Dept: 9][No Urut][Tgl & Thn Lahir]
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small">Tingkatan / Hak Akses Kerja</label>
                    <select name="role" class="form-select" required>
                        <option value="operator" selected>Operator (Pelaksana Lapangan)</option>
                        <option value="sub_leader">Sub Leader</option>
                        <option value="leader">Leader</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Simpan Akun Operator</button>
            </form>
        </div>
    </div>
</body>

</html>