@extends('layouts.rh')

@section('title', 'Gestion des Affiliations')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Affiliations</h1>
        <a href="{{ route('affiliations.create') }}" class="btn btn-primary">➕ Nouvelle affiliation</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Statistiques --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h2 class="display-4">{{ $stats['total'] }}</h2>
                    <h5>Total</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h2 class="display-4">{{ $stats['valides'] }}</h2>
                    <h5>Valides</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h2 class="display-4">{{ $stats['expirants'] }}</h2>
                    <h5>Expirants</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h2 class="display-4">{{ $stats['expirés'] }}</h2>
                    <h5>Expirés</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Liste des affiliations</h5>
            <div>
                <select class="form-select d-inline-block w-auto" onchange="filterAffiliations(this.value)">
                    <option value="">Tous les statuts</option>
                    <option value="Valide">Valides</option>
                    <option value="Expirant">Expirants</option>
                    <option value="Expiré">Expirés</option>
                    <option value="Invalide">Invalides</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="affiliations-table">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>Numéro</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($affiliations as $affiliation)
                            <tr data-statut="{{ $affiliation->statut_affiliation }}">
                                <td>
                                    <strong>{{ $affiliation->employe->nom }} {{ $affiliation->employe->prenom }}</strong><br>
                                    <small class="text-muted">{{ $affiliation->employe->poste }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $affiliation->type == 'cnaps' ? 'primary' : ($affiliation->type == 'ostie' ? 'info' : 'success') }}">
                                        {{ $affiliation->type_formatted }}
                                    </span>
                                </td>
                                <td>{{ $affiliation->numero_affiliation }}</td>
                                <td>{{ $affiliation->date_debut->format('d/m/Y') }}</td>
                                <td>
                                    @if($affiliation->date_expiration)
                                        <span class="{{ $affiliation->statut_badge }}">{{ $affiliation->date_expiration->format('d/m/Y') }}</span>
                                    @else
                                        <span class="badge bg-secondary">Permanent</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="{{ $affiliation->statut_badge }}">{{ $affiliation->statut_affiliation }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('affiliations.show', $affiliation) }}" class="btn btn-outline-primary">Voir</a>
                                        <a href="{{ route('affiliations.edit', $affiliation) }}" class="btn btn-outline-warning">Modifier</a>
                                        @if($affiliation->statut_affiliation == 'Expiré' || $affiliation->statut_affiliation == 'Expirant')
                                            <a href="{{ route('affiliations.renouveler', $affiliation) }}" class="btn btn-outline-success">Renouveler</a>
                                        @endif
                                        <form action="{{ route('affiliations.destroy', $affiliation) }}" method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aucune affiliation trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $affiliations->links() }}
        </div>
    </div>

    <script>
        function filterAffiliations(statut) {
            const rows = document.querySelectorAll('#affiliations-table tbody tr');
            rows.forEach(row => {
                if (statut === '' || row.dataset.statut === statut) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</div>
@endsection