@extends('layouts.candidat')

@section('title', 'Annonces Disponibles')

@section('content')
<h1>Annonces Disponibles</h1>
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
<ul class="list-group">
    @foreach($annonces as $annonce)
        <li class="list-group-item">
            <h5>{{ $annonce->titre }}</h5>
            <p>{{ $annonce->description }}</p>
            <a href="{{ route('candidats.postuler', $annonce->id) }}" class="btn btn-primary btn-sm">Postuler</a>
        </li>
    @endforeach
</ul>
@endsection