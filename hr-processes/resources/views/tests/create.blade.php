@extends('layouts.rh')

@section('title', 'Nouveau Test QCM')

@section('content')
<div class="container">
    <h1>Nouveau Test QCM</h1>

    <form action="{{ route('tests.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Titre du test</label>
                            <input type="text" name="titre" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Durée (minutes)</label>
                            <input type="number" name="duree_minutes" class="form-control" min="5" max="120" value="30" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Nombre de questions</label>
                            <input type="number" name="nombre_questions" class="form-control" min="1" max="50" value="10" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Statut</label>
                            <select name="statut" class="form-control">
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Créer le test</button>
                <a href="{{ route('tests.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </div>
    </form>
</div>
@endsection