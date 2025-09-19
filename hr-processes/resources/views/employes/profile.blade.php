@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Mon Profil Employé</h1>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                Informations
            </div>
            <div class="card-body">
                <p><strong>Nom :</strong> {{ $employe->nom }} {{ $employe->prenom }}</p>
                <p><strong>Poste :</strong> {{ $employe->poste }}</p>
                <p><strong>Salaire :</strong> {{ $employe->salaire }}</p>
                <p><strong>Compétences :</strong> {{ $employe->competences ?? 'N/A' }}</p>
                <p><strong>Historique :</strong> {{ $employe->historique ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Mettre à jour le Profil
            </div>
            <div class="card-body">
                <form action="{{ route('employes.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label>Compétences :</label>
                        <input type="text" name="competences" value="{{ $employe->competences }}" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label>Historique :</label>
                        <textarea name="historique" class="form-control" rows="3">{{ $employe->historique }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>
@endsection