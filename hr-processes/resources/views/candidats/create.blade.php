@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Ajouter un candidat</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('candidats.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nom :</label>
                <input type="text" name="nom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Prénom :</label>
                <input type="text" name="prenom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Âge :</label>
                <input type="number" name="age" min="18" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Diplôme :</label>
                <input type="text" name="diplome" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">CV (PDF, max 2MB) :</label>
                <input type="file" name="cv" class="form-control" accept=".pdf">
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="{{ route('candidats.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
@endsection