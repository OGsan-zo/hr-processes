@extends('layouts.candidat')

@section('title', 'Postuler à : ' . $annonce->titre)

@section('content')
<h1>Postuler pour : {{ $annonce->titre }}</h1>
<form method="POST" action="{{ route('candidats.postuler', $annonce->id) }}">
    @csrf
    <p>Votre CV existant sera utilisé.</p>
    <button type="submit" class="btn btn-primary">Postuler</button>
</form>
@endsection