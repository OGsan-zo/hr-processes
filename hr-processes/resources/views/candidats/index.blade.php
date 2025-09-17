@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Rechercher des Candidats</h1>
        <form method="GET" action="{{ route('candidats.index') }}">
            <div class="form-group">
                <label for="age">Âge :</label>
                <input type="number" name="age" id="age" class="form-control" value="{{ request('age') }}">
            </div>
            <div class="form-group">
                <label for="diplome">Diplôme :</label>
                <input type="text" name="diplome" id="diplome" class="form-control" value="{{ request('diplome') }}">
            </div>
            <div class="form-group">
                <label for="adresse">Adresse :</label>
                <input type="text" name="adresse" id="adresse" class="form-control" value="{{ request('adresse') }}">
            </div>
            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>

        <table class="table mt-4">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Âge</th>
                    <th>Diplôme</th>
                    <th>Adresse</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($candidats as $candidat)
                    <tr>
                        <td>{{ $candidat->nom }}</td>
                        <td>{{ $candidat->prenom }}</td>
                        <td>{{ $candidat->age }}</td>
                        <td>{{ $candidat->diplome }}</td>
                        <td>{{ $candidat->adresse }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection