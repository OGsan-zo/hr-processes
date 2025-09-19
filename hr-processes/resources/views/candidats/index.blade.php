@extends('layouts.rh')

@section('content')
    <div class="container">
        <h1>Liste des Candidats</h1>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire de recherche/filtrage -->
        <form method="GET" action="{{ route('candidats.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <label>Âge :</label>
                    <input type="number" name="age" value="{{ request('age') }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Diplôme :</label>
                    <input type="text" name="diplome" value="{{ request('diplome') }}" class="form-control" placeholder="ex: Licence">
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                    <a href="{{ route('candidats.index') }}" class="btn btn-secondary">Réinitialiser</a>
                </div>
            </div>
        </form>

        <hr>

        <!-- Tableau des candidats avec formulaire de transformation -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Âge</th>
                    <th>Diplôme</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($candidats as $candidat)
                    <tr>
                        <td>{{ $candidat->nom }}</td>
                        <td>{{ $candidat->prenom }}</td>
                        <td>{{ $candidat->age }}</td>
                        <td>{{ $candidat->diplome ?? 'N/A' }}</td>
                        <td>
                            <!-- Formulaire de transformation en employé -->
                            <form action="{{ route('candidats.transform', $candidat) }}" method="POST" class="d-inline">
                                @csrf
                                <div class="input-group input-group-sm mb-2">
                                    <input type="text" name="poste" placeholder="Poste" required class="form-control" style="width: 120px;">
                                    <input type="number" name="salaire" placeholder="Salaire" required class="form-control" style="width: 100px;">
                                    <input type="text" name="competences" placeholder="Compétences" class="form-control" style="width: 150px;">
                                    <button type="submit" class="btn btn-success btn-sm">→ Employé</button>
                                </div>
                            </form>
                            <br>
                            <small class="text-muted">
                                @if($candidat->cv)
                                    CV: <a href="{{ asset('storage/' . $candidat->cv) }}" target="_blank">Voir</a>
                                @else
                                    Pas de CV
                                @endif
                            </small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Aucun candidat trouvé</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <a href="{{ route('candidats.create') }}" class="btn btn-primary">Ajouter un candidat</a>
    </div>
@endsection