@extends('layouts.rh')

@section('content')
    <h1>Postuler pour : {{ $annonce->titre }}</h1>
    <form method="POST" action="{{ route('candidat.postuler', $annonce->id) }}">
        @csrf
        <!-- Pas de champ CV, car déjà stocké -->
        <p>Votre CV existant sera utilisé.</p>
        <!-- Ajoutez d'autres champs si besoin, ex. <textarea name="motivation"></textarea> -->
        <button type="submit">Postuler</button>
    </form>
@endsection