@extends('layouts.rh')

@section('content')
    <div class="container">
        <h1>Classement des Candidats</h1>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Formulaire de sélection d'annonce -->
        <form method="GET" action="{{ route('candidats.classify') }}" class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Sélectionner une annonce :</label>
                    <select name="annonce_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Toutes les annonces</option>
                        @foreach($annonces as $annonce)
                            <option value="{{ $annonce->id }}" {{ request('annonce_id') == $annonce->id ? 'selected' : '' }}>
                                {{ $annonce->titre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                    <form method="POST" action="{{ route('candidats.classify.post') }}" style="display: inline;">
                        @csrf
                        <input type="hidden" name="annonce_id" value="{{ request('annonce_id') }}">
                        <input type="hidden" name="calcule_scores" value="1">
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Calculer les scores pour tous les candidats ?')">
                            🔄 Recalculer Scores
                        </button>
                    </form>
                </div>
            </div>
        </form>

        <!-- Tableau des candidats classés -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Score Global</th>
                    <th>Nom</th>
                    <th>Âge</th>
                    <th>Diplôme</th>
                    <th>Compétences</th>
                    <th>Score Compétences</th>
                    <th>Score Profil</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($candidats as $candidat)
                    <tr class="table-{{ $candidat->score_global >= 80 ? 'success' : ($candidat->score_global >= 60 ? 'warning' : 'secondary') }}">
                        <td><strong>{{ $candidat->score_global }}/100</strong></td>
                        <td>{{ $candidat->nom }} {{ $candidat->prenom }}</td>
                        <td>{{ $candidat->age }}</td>
                        <td>{{ $candidat->diplome ?? 'N/A' }}</td>
                        <td>
                            @if($candidat->competences)
                                <span class="badge bg-primary">{{ $candidat->competences }}</span>
                            @else
                                <span class="text-muted">Aucune</span>
                            @endif
                        </td>
                        <td>{{ $candidat->score_competences }}/100</td>
                        <td>{{ $candidat->score_profil }}/100</td>
                        <td>
                            <a href="{{ route('candidats.migrate', $candidat) }}" class="btn btn-sm btn-success">→ Employé</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Aucun candidat trouvé</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection