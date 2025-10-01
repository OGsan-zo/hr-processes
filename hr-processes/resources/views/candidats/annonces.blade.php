@extends('layouts.candidat')

@section('title', 'Annonces Disponibles')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-primary">
                <i class="fas fa-bullhorn me-2"></i>Annonces Disponibles
            </h1>
            <div class="text-muted">
                {{ $annonces->count() }} offre(s) trouvée(s)
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-3">
    @foreach($annonces as $annonce)
    <div class="col-12">
        <div class="card card-hover shadow-sm border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="card-title text-primary mb-2">{{ $annonce->titre }}</h5>
                        <p class="card-text text-muted mb-3">{{ $annonce->description }}</p>
                        <div class="d-flex gap-2">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $annonce->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('candidats.postuler', $annonce->id) }}" class="btn btn-primary-custom btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Postuler
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($annonces->count() === 0)
<div class="row mt-5">
    <div class="col-12">
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Aucune annonce disponible</h4>
            <p class="text-muted">Revenez plus tard pour découvrir de nouvelles opportunités.</p>
        </div>
    </div>
</div>
@endif
@endsection