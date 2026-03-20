<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Automated Timetable Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(1200px 700px at 10% 10%, rgba(109,40,217,.22), transparent 55%),
                radial-gradient(900px 600px at 90% 20%, rgba(2,6,23,.24), transparent 60%),
                linear-gradient(180deg, #10122a 0%, #070813 55%, #070813 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
            border: 1px solid rgba(255,255,255,.18);
        }
        .login-header {
            background: linear-gradient(135deg, #6d28d9 0%, #7c3aed 55%, #4c1d95 140%);
            color: #fff;
            padding: 30px;
            text-align: center;
        }
        .login-header i {
            font-size: 3rem;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        .login-body {
            padding: 35px 40px;
        }
        .form-control:focus {
            border-color: rgba(109,40,217,.70);
            box-shadow: 0 0 0 0.25rem rgba(109,40,217,.18);
        }
        .btn-login {
            background: linear-gradient(135deg, #6d28d9 0%, #7c3aed 55%, #4c1d95 140%);
            border: none;
            padding: 12px;
            font-weight: 600;
            box-shadow: 0 12px 28px rgba(109,40,217,.28);
        }
        .btn-login:hover {
            filter: brightness(0.98);
            border: none;
            transform: translateY(-1px);
            box-shadow: 0 16px 34px rgba(109,40,217,.36);
        }
        .invalid-feedback { display: block; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-calendar-alt"></i>
            <h4 class="mb-0">ATG System</h4>
            <p class="mb-0 mt-1 small opacity-90">Sign in to continue</p>
        </div>
        <div class="login-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required autofocus placeholder="admin@example.com">
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i> Sign In
                </button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
