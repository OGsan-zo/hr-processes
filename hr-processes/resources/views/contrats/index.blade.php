@extends('layouts.rh')

@section('content')

<h1>Liste des contrats</h1>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<a href="{{ route('contrats.create') }}">Nouveau contrat</a>

<ul>
@foreach($contrats as $contrat)
    <li>
        Employé : {{ $contrat->employe->nom }} ({{ $contrat->type }}) <br>
        Du {{ $contrat->date_debut }} au {{ $contrat->date_fin }} <br>
        Durée : {{ $contrat->duree_jours }} jours <br>
        Statut : {{ $contrat->statut_calcule }}
    </li>
@endforeach
</ul>
@endsection