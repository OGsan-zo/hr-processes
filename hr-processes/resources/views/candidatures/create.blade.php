@extends('layouts.app')

@section('content')
<h1>Nouvelle candidature</h1>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<form action="{{ route('candidatures.store') }}" method="POST" enctype="multipart/form-data">
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

    <label>CV (PDF ou DOC) :</label>
    <input type="file" name="cv" accept=".pdf,.doc,.docx" required><br><br>

    <button type="submit">Postuler</button>
</form>
@endsection