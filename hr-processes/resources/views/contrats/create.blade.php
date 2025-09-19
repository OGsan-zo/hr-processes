@extends('layouts.rh')

@section('content')
<h1>Créer un contrat</h1>

<form action="{{ route('contrats.store') }}" method="POST">
    @csrf
    <label>Employé :</label>
    <select name="employe_id" required>
        @foreach($employes as $employe)
            <option value="{{ $employe->id }}">{{ $employe->nom }} {{ $employe->prenom }}</option>
        @endforeach
    </select><br><br>

    <label>Type :</label>
    <select name="type">
        <option value="essai">Essai</option>
        <option value="cdi">CDI</option>
        <option value="cdd">CDD</option>
    </select><br><br>

    <label>Date début :</label>
    <input type="date" name="date_debut" required><br><br>

    <label>Date fin :</label>
    <input type="date" name="date_fin" required><br><br>

    <label>Renouvellements :</label>
    <input type="number" name="renouvellements" min="0" max="1" value="0"><br><br>

    <button type="submit">Enregistrer</button>
</form>
@endsection
