@extends('layouts.rh')

@section('content')
<h1>Nouvelle annonce</h1>
<form action="{{ route('annonces.store') }}" method="POST">
    @csrf
    <label>Titre :</label>
    <input type="text" name="titre" required><br><br>

    <label>Description :</label>
    <textarea name="description" required></textarea><br><br>

    <label>Date début :</label>
    <input type="date" name="date_debut"><br><br>

    <label>Date fin :</label>
    <input type="date" name="date_fin"><br><br>

    <button type="submit">Publier</button>
</form>
@endsection