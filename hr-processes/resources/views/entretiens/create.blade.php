
@extends('layouts.app')

@section('content')
<h1>Planifier un entretien</h1>

<form action="{{ route('entretiens.store') }}" method="POST">
    @csrf
    <label>Candidat :</label>
    <select name="candidat_id" required>
        @foreach($candidats as $candidat)
            <option value="{{ $candidat->id }}">{{ $candidat->nom }} {{ $candidat->prenom }}</option>
        @endforeach
    </select><br><br>

    <label>Annonce :</label>
    <select name="annonce_id" required>
        @foreach($annonces as $annonce)
            <option value="{{ $annonce->id }}">{{ $annonce->titre }}</option>
        @endforeach
    </select><br><br>

    <label>Date et heure :</label>
    <input type="datetime-local" name="date_entretien" required><br><br>

    <label>Durée (min) :</label>
    <input type="number" name="duree" value="60"><br><br>

    <button type="submit">Planifier</button>
</form>
@endsection