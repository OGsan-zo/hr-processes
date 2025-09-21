@extends('layouts.rh')

@section('title', 'Dashboard RH')

@section('content')
<div class="container-fluid">
    {{-- En-tête --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-1">Dashboard RH</h1>
                    <p class="text-muted mb-0">Bienvenue, {{ auth()->user()->name }} ({{ $role }})</p>
                </div>
                <div class="alert alert-info d-inline-block">
                    <i class="fas fa-crown me-1"></i>
                    Rôle : 
                    @if($role == 'admin')
                        <span class="badge bg-primary">👑 Admin RH</span>
                    @elseif($role == 'manager')
                        <span class="badge bg-success">👨‍💼 Manager</span>
                    @elseif($role == 'employe')
                        <span class="badge bg-warning">👤 Employé</span>
                    @else
                        <span class="badge bg-secondary">Non assigné</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Statistiques principales --}}
    @if($role == 'admin' || $role == 'manager')
        <div class="row mb-4 g-3">
            {{-- Candidats --}}
            <div class="col-xl-2 col-md-3 col-sm-6">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Candidats
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['candidats'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Annonces --}}
            <div class="col-xl-2 col-md-3 col-sm-6">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Annonces actives
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['annonces'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Candidatures --}}
            <div class="col-xl-2 col-md-3 col-sm-6">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Candidatures
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['candidatures'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Employés --}}
            <div class="col-xl-2 col-md-3 col-sm-6">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Employés
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['employes'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tests --}}
            <div class="col-xl-2 col-md-3 col-sm-6">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Tests QCM
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['tests'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-flask fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Affiliations --}}
            <div class="col-xl-2 col-md-3 col-sm-6">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Affiliations
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['affiliations'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-building fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Candidatures par statut --}}
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Statut des candidatures</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            @if(isset($candidaturesParStatut))
                                @foreach($candidaturesParStatut as $statut => $count)
                                    <div class="col-md-4 mb-3">
                                        <div class="card bg-{{ $statut == 'en_attente' ? 'secondary' : ($statut == 'accepte' ? 'success' : 'danger') }} text-white">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ ucfirst(str_replace('_', ' ', $statut)) }}</h5>
                                                <h2 class="display-4">{{ $count }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Aucune candidature pour le moment
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Actions rapides</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('candidats.create') }}" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>Nouveau candidat
                            </a>
                            <a href="{{ route('annonces.create') }}" class="btn btn-success">
                                <i class="fas fa-bullhorn me-2"></i>Nouvelle annonce
                            </a>
                            <a href="{{ route('candidats.classify') }}" class="btn btn-info">
                                <i class="fas fa-chart-bar me-2"></i>Classer candidats
                            </a>
                            <a href="{{ route('tests.create') }}" class="btn btn-warning">
                                <i class="fas fa-flask me-2"></i>Nouveau test QCM
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top candidats --}}
        @if(isset($topCandidats) && $topCandidats->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Top 5 candidats par score</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Score</th>
                                            <th>Compétences</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topCandidats as $candidat)
                                            <tr>
                                                <td>
                                                    <strong>{{ $candidat->nom }} {{ $candidat->prenom }}</strong>
                                                    <br><small class="text-muted">{{ $candidat->diplome ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge fs-6 {{ $candidat->score_global >= 80 ? 'bg-success' : ($candidat->score_global >= 60 ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ $candidat->score_global }}/100
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $candidat->competences ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('candidats.migrate', $candidat) }}" class="btn btn-sm btn-success" onclick="return confirm('Migrer ce candidat ?')">
                                                        🚀 Migrer
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Section Employé --}}
        @if($role == 'employe')
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">Mon espace personnel</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fas fa-user-circle fa-5x text-muted mb-3"></i>
                            <h4>Gestion de votre profil</h4>
                            <p class="text-muted">Accédez à votre espace personnel pour gérer vos informations et compétences.</p>
                            <a href="{{ route('employes.profile') }}" class="btn btn-warning btn-lg">
                                <i class="fas fa-edit me-2"></i>Mon profil
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Ressources utiles</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <a href="https://laravel.com/docs" target="_blank" class="list-group-item list-group-item-action">
                                    <i class="fas fa-book me-2"></i>Docs Laravel
                                </a>
                                <a href="https://spatie.be/docs/laravel-permission" target="_blank" class="list-group-item list-group-item-action">
                                    <i class="fas fa-shield-alt me-2"></i>Gestion des rôles
                                </a>
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="list-group-item list-group-item-action text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                </a>
                            </div>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection