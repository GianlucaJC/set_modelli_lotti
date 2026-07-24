<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imposta CIFF | Set Modelli Lotti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --brand-navy: #123a5d; --brand-teal: #087a80; --ink: #14233a; }
        body { background: #f1f5f7; color: var(--ink); }
        .app-header { background: var(--brand-navy); }
        .brand-mark { width: 40px; height: 40px; display: grid; place-items: center; background: var(--brand-teal); border-radius: 8px; }
        .workspace { max-width: 840px; }
        .card { border: 0; border-radius: 8px; box-shadow: 0 .75rem 2.5rem rgba(18, 58, 93, .1); }
        .form-control, .form-select { min-height: 50px; border-color: #c7d6df; }
        .form-control:focus, .form-select:focus { border-color: var(--brand-teal); box-shadow: 0 0 0 .25rem rgba(8, 122, 128, .15); }
        .btn-primary { --bs-btn-bg: var(--brand-teal); --bs-btn-border-color: var(--brand-teal); --bs-btn-hover-bg: #05666b; --bs-btn-hover-border-color: #05666b; }
    </style>
</head>
<body>
    <header class="app-header shadow-sm">
        <div class="container py-3 d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2 text-white">
                <div class="brand-mark" aria-hidden="true"><i class="bi bi-box-seam"></i></div>
                <div>
                    <p class="text-white-50 small mb-0">Gestione operativa</p>
                    <h1 class="h5 fw-bold mb-0">Set Modelli Lotti</h1>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm" title="Esci dall'applicazione">
                    <i class="bi bi-box-arrow-right me-1"></i>Esci
                </button>
            </form>
        </div>
    </header>

    <main class="container workspace py-5">
        <div class="mb-4">
            <p class="text-uppercase text-secondary small fw-semibold mb-1">Configurazione</p>
            <h2 class="h3 fw-bold mb-1">Imposta modello CIFF</h2>
            <p class="text-secondary mb-0">Associa il file modello al lotto selezionato.</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success d-flex align-items-center gap-2 border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <div id="current-recipe" class="alert alert-info d-none align-items-center gap-2 border-0 shadow-sm" role="status">
            <i class="bi bi-info-circle-fill"></i>
            <span>La ricetta attualmente settata è <strong id="current-recipe-value"></strong>.</span>
        </div>

        <section class="card">
            <div class="card-body p-4 p-md-5">
                <form method="POST" action="{{ route('lots.update') }}">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="lot_number" class="form-label fw-semibold">Lotto</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-secondary"><i class="bi bi-upc-scan"></i></span>
                                <input id="lot_number" name="lot_number" type="text" class="form-control @error('lot_number') is-invalid @enderror" value="{{ old('lot_number') }}" required autofocus placeholder="Es. LOT-001" data-recipe-url="{{ route('lots.current-recipe') }}">
                                @error('lot_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="ciff_model" class="form-label fw-semibold">DBmodelli</label>
                            <select id="ciff_model" name="ciff_model" class="form-select @error('ciff_model') is-invalid @enderror" required>
                                <option value="">Seleziona un modello</option>
                                @foreach ($ciffModels as $ciffModel)
                                    <option value="{{ $ciffModel }}" @selected(old('ciff_model') === $ciffModel)>{{ $ciffModel }}</option>
                                @endforeach
                            </select>
                            @error('ciff_model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-5 pt-4 border-top">
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi bi-check2-circle me-2"></i>Imposta Ciff
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </main>
    <script>
        const lotInput = document.getElementById('lot_number');
        const recipeAlert = document.getElementById('current-recipe');
        const recipeValue = document.getElementById('current-recipe-value');

        async function loadCurrentRecipe() {
            const lotNumber = lotInput.value.trim();

            recipeAlert.classList.add('d-none');
            recipeAlert.classList.remove('d-flex');

            if (!lotNumber) {
                return;
            }

            const response = await fetch(`${lotInput.dataset.recipeUrl}?lot_number=${encodeURIComponent(lotNumber)}`, {
                headers: {'Accept': 'application/json'},
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();

            if (data.recipe) {
                recipeValue.textContent = data.recipe;
                recipeAlert.classList.remove('d-none');
                recipeAlert.classList.add('d-flex');
            }
        }

        lotInput.addEventListener('change', loadCurrentRecipe);
        lotInput.addEventListener('blur', loadCurrentRecipe);
    </script>
</body>
</html>