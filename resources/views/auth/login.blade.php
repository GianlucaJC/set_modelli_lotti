<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accesso | Set Modelli Lotti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --brand-navy: #123a5d; --brand-teal: #087a80; --ink: #14233a; }
        body { background: #edf3f7; color: var(--ink); }
        .login-page { min-height: 100vh; background: linear-gradient(135deg, #e6f2f5 0%, #f8fbfc 50%, #d9e9f1 100%); }
        .login-panel { max-width: 440px; }
        .brand-mark { width: 58px; height: 58px; display: grid; place-items: center; background: var(--brand-teal); color: #fff; border-radius: 8px; font-size: 1.6rem; }
        .card { border: 0; border-radius: 8px; box-shadow: 0 1rem 3rem rgba(18, 58, 93, .12); }
        .form-control { min-height: 48px; border-color: #c7d6df; }
        .form-control:focus { border-color: var(--brand-teal); box-shadow: 0 0 0 .25rem rgba(8, 122, 128, .15); }
        .btn-primary { --bs-btn-bg: var(--brand-navy); --bs-btn-border-color: var(--brand-navy); --bs-btn-hover-bg: #0d2d49; --bs-btn-hover-border-color: #0d2d49; min-height: 48px; }
    </style>
</head>
<body class="login-page">
    <main class="container d-flex min-vh-100 align-items-center py-5">
        <section class="login-panel w-100 mx-auto">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="brand-mark" aria-hidden="true"><i class="bi bi-box-seam"></i></div>
                <div>
                    <p class="text-uppercase text-secondary small fw-semibold mb-1">Gestione operativa</p>
                    <h1 class="h3 fw-bold mb-0">Set Modelli Lotti</h1>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-4 p-md-5">
                    <h2 class="h4 fw-bold mb-1">Accedi</h2>
                    <p class="text-secondary mb-4">Inserisci le credenziali per proseguire.</p>

                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold">Nome utente</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-person"></i></span>
                                <input id="username" name="username" type="text" class="form-control border-start-0 @error('username') is-invalid @enderror" value="{{ old('username') }}" required autofocus autocomplete="username">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-lock"></i></span>
                                <input id="password" name="password" type="password" class="form-control border-start-0 @error('password') is-invalid @enderror" required autocomplete="current-password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Accedi
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>
</html>