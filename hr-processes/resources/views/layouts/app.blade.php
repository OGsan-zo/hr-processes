<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion RH - @yield('title')</title>
    <!-- Bootstrap CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">Gestion RH</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('candidats.index') }}">Candidats</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('candidats.create') }}">Nouveau Candidat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('annonces.index') }}">Annonces</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('annonces.create') }}">Nouvelle Annonce</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('candidatures.create') }}">Nouvelle Candidature</a>
                    </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link" href="{{ route('candidatures.selection') }}">Sélections</a>
                        </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('employes.create') }}">Nouvel Employé</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container mt-4">
        @yield('content')
    </div>

    <!-- Bootstrap JS et Popper.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>