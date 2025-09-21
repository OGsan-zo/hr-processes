@extends('layouts.rh')

@section('title', 'Affiliation : ' . $affiliation->type_formatted)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $affiliation->type_formatted }} - {{ $affiliation->numero_affiliation }}</h1>
        <div>
            <a href="{{ route('affiliations.edit', $affiliation) }}" class="btn btn-warning me-2">✏️ Modifier</a>
            @if($affiliation->statut_affiliation == 'Expiré' || $affiliation->statut_affiliation == 'Expirant')
                <a href="{{ route('affiliations.renouveler', $affiliation) }}" class="btn btn-success me-2">🔄 Renouveler</a>
            @endif
            <a href="{{ route('affiliations.index') }}" class="btn btn-secondary">← Retour</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Informations principales</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold">Employé :</td>
                            <td>{{ $affiliation->employe->nom }} {{ $affiliation->employe->prenom }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Poste :</td>
                            <td>{{ $affiliation->employe->poste }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Type :</td>
                            <td><span class="badge bg-primary">{{ $affiliation->type_formatted }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Numéro :</td>
                            <td><code>{{ $affiliation->numero_affiliation }}</code></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Date début :</td>
                            <td>{{ $affiliation->date_debut->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Date fin :</td>
                            <td>
                                @if($affiliation->date_expiration)
                                    <span class="{{ $affiliation->statut_badge }}">
                                        {{ $affiliation->date_expiration->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="badge bg-success">Permanent</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Statut :</td>
                            <td><span class="{{ $affiliation->statut_badge }}">{{ $affiliation->statut_affiliation }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Documents</h5>
                </div>
                <div class="card-body">
                    @if($affiliation->documents && count($affiliation->documents) > 0)
                        <div class="list-group">
                            @foreach($affiliation->documents as $index => $document)
                                <a href="{{ Storage::url($document) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file-pdf me-2"></i>
                                        Document {{ $index + 1 }}
                                    </div>
                                    <span class="badge bg-primary rounded-pill">PDF</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun document joint</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Historique</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-time">
                                <span class="date">{{ $affiliation->created_at->format('d/m/Y') }}</span>
                                <span class="time">{{ $affiliation->created_at->format('H:i') }}</span>
                            </div>
                            <div class="timeline-content">
                                <h5>Affiliation créée</h5>
                                <p>Affiliation {{ $affiliation->type_formatted }} n°{{ $affiliation->numero_affiliation }} créée pour {{ $affiliation->employe->nom }} {{ $affiliation->employe->prenom }}</p>
                            </div>
                        </div>
                        @if($affiliation->updated_at > $affiliation->created_at)
                            <div class="timeline-item">
                                <div class="timeline-time">
                                    <span class="date">{{ $affiliation->updated_at->format('d/m/Y') }}</span>
                                    <span class="time">{{ $affiliation->updated_at->format('H:i') }}</span>
                                </div>
                                <div class="timeline-content">
                                    <h5>Affiliation modifiée</h5>
                                    <p>Modifications apportées à l'affiliation</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection