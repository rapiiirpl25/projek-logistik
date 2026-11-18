<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Manajemen Tugas Logistik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center min-vh-100">
    <div class="container" style="max-width: 420px;">
        <div class="card border-0 shadow-sm p-4 rounded-4">
            <h4 class="fw-bold text-center mb-1">Logistik App</h4>
            <p class="text-muted text-center small mb-4">Masuk menggunakan Kode Akses</p>

            @if($errors->any())
                <div class="alert alert-danger p-2 small">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Kode Akses</label>
                    <input type="text" name="code" class="form-control text-uppercase" maxlength="12"
                        value="{{ old('code') }}" autocomplete="off" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-semibold">Masuk</button>
            </form>
        </div>
    </div>
</body>

</html>