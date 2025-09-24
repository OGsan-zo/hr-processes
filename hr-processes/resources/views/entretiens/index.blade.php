@extends('layouts.rh')

@section('content')
<h1>Planning des entretiens</h1>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<a href="{{ route('entretiens.create') }}">Planifier un nouvel entretien</a>

<ul>
@foreach($entretiens as $entretien)
    <li>
        {{ $entretien->candidat->nom }} - {{ $entretien->annonce->titre }} :
        {{ $entretien->date_entretien }} ({{ $entretien->duree }} min) - [{{ $entretien->statut }}]
    </li>
@endforeach
</ul>
@endsection