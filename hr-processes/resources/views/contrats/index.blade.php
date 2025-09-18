@extends('layouts.app')

@section('content')

<h1>Liste des contrats</h1>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<a href="{{ route('contrats.create') }}">Nouveau contrat</a>

<ul>
@foreach($contrats as $contrat)
    <li>
        Employé : {{ $contrat->employe->nom }} ({{ $contrat->type }})
        - du {{ $contrat->date_debut }} au {{ $contrat->date_fin }}
        - Renouvellements : {{ $contrat->renouvellements }}
    </li>
@endforeach
</ul>
@endsection
