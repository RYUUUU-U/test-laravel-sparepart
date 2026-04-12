<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - Inventory Sparepart Motor</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-secondary d-flex align-items-center justify-content-center" style="height: 100vh;">

    <div class="card shadow p-4" style="width: 400px;">
        <div class="text-center mb-4">
            <i class="fa-solid fa-motorcycle fa-2x text-dark mb-2"></i>
            <h4>Sistem Inventory</h4>
            <span class="text-muted">Sparepart Motor</span>
        </div>

        {{-- Tampilkan pesan error --}}
        @if(session('error'))
            <div class="alert alert-danger text-center py-2" style="font-size: 14px;">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                       placeholder="admin / kasir" value="{{ old('username') }}" required>
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="Masukan password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="fa-solid fa-right-to-bracket me-1"></i> LOGIN
            </button>
        </form>

        <div class="text-center mt-3 border-top pt-3">
            <small class="text-muted d-block">Akun Demo:</small>
            <small class="text-primary"><b>admin</b> (Pass: 123)</small> |
            <small class="text-success"><b>kasir</b> (Pass: 123)</small>
        </div>
    </div>

</body>
</html>
