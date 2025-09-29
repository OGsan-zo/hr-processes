@extends('layouts.rh')

@section('content')
<h1>Annonces Disponibles</h1>
<ul>
    @foreach($annonces as $annonce)
        <li>
            {{ $annonce->titre }} - {{ $annonce->description }}
            <a href="{{ route('candidat.postuler', $annonce->id) }}">Postuler</a>
        </li>
    @endforeach
</ul>
@endsection