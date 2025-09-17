@extends('layouts.app')

@section('content')
<h1>Modifier une annonce</h1>
<form action="{{ route('annonces.update', $annonce) }}" method="POST">
    @csrf @method('PUT')
    <label>Titre :</label>
    <input type="text" name="titre" value="{{ $annonce->titre }}" required><br><br>

    <label>Description :</label>
    <textarea name="description">{{ $annonce->description }}</textarea><br><br>

    <label>Date début :</label>
    <input type="date" name="date_debut" value="{{ $annonce->date_debut }}"><br><br>

    <label>Date fin :</label>
    <input type="date" name="date_fin" value="{{ $annonce->date_fin }}"><br><br>

    <label>Statut :</label>
    <select name="statut">
        <option value="active" {{ $annonce->statut == 'active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ $annonce->statut == 'inactive' ? 'selected' : '' }}>Inactive</option>
        <option value="expiree" {{ $annonce->statut == 'expiree' ? 'selected' : '' }}>Expirée</option>
    </select><br><br>

    <button type="submit">Mettre à jour</button>
</form>
@endsection