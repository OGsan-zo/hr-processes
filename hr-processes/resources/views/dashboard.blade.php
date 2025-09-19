@extends('layouts.rh')

@section('title', 'Dashboard RH')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-3">Dashboard - Gestion RH</h1>
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Badge rôle --}}
            <div class="alert alert-info d-inline-block">
                <strong>Rôle :</strong> 
                @if(auth()->user()->hasRole('admin'))
                    <span class="badge bg-primary">👑 Admin RH</span>
                @elseif(auth()->user()->hasRole('manager'))
                    <span class="badge bg-success">👨‍💼 Manager</span>
                @elseif(auth()->user()->hasRole('employe'))
                    <span class="badge bg-warning">👤 Employé</span>
                @else
                    <span class="badge bg-secondary">Non assigné</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Statistiques - Visible Admin/Manager --}}
    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h2 class="display-4">{{ $stats['candidats'] ?? 0 }}</h2>
                        <h5>Candidats</h5>
                        <a href="{{ route('candidats.index') }}" class="btn btn-light btn-sm">Voir tous</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h2 class="display-4">{{ $stats['annonces'] ?? 0 }}</h2>
                        <h5>Annonces</h5>
                        <a href="{{ route('annonces.index') }}" class="btn btn-light btn-sm">Voir tous</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h2 class="display-4">{{ $stats['candidatures'] ?? 0 }}</h2>
                        <h5>Candidatures</h5>
                        <a href="{{ route('candidatures.selection') }}" class="btn btn-light btn-sm">Voir toutes</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h2 class="display-4">{{ $stats['employes'] ?? 0 }}</h2>
                        <h5>Employés</h5>
                        <a href="{{ route('employes.create') }}" class="btn btn-light btn-sm">Gérer</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Candidatures par statut --}}
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Candidatures par statut</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(isset($candidaturesParStatut))
                                @foreach($candidaturesParStatut as $statut => $count)
                                    <div class="col-md-4 mb-3">
                                        <div class="card bg-{{ $statut == 'en_attente' ? 'secondary' : ($statut == 'accepte' ? 'success' : 'danger') }} text-white">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">{{ ucfirst($statut) }}</h6>
                                                <h3 class="card-text">{{ $count }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <p class="text-muted">Aucune donnée disponible</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Actions rapides</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('candidats.create') }}" class="btn btn-primary w-100 mb-2 d-block">
                            ➕ Nouveau candidat
                        </a>
                        <a href="{{ route('annonces.create') }}" class="btn btn-success w-100 mb-2 d-block">
                            📢 Nouvelle annonce
                        </a>
                        <a href="{{ route('candidats.classify') }}" class="btn btn-info w-100 d-block">
                            📊 Classer candidats
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Section Employé --}}
    @if(auth()->user()->hasRole('employe'))
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Mon profil</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Cliquez ci-dessous pour gérer votre profil personnel.</p>
                        <a href="{{ route('employes.profile') }}" class="btn btn-primary">👤 Voir mon profil</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Ressources</h5>
                    </div>
                    <div class="card-body">
                        <a href="https://laravel.com/docs" target="_blank" class="btn btn-outline-primary w-100 mb-2 d-block">
                            📚 Documentation
                        </a>
                        <a href="https://spatie.be/docs/laravel-permission" target="_blank" class="btn btn-outline-secondary w-100 d-block">
                            🔐 Permissions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection