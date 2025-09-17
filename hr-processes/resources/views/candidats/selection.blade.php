@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Résultats des Sélections</h1>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th>Candidat</th>
                    <th>Annonce</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($candidatures as $candidature)
                    <tr>
                        <td>{{ $candidature->candidat->nom }} {{ $candidature->candidat->prenom }}</td>
                        <td>{{ $candidature->annonce->titre }}</td>
                        <td>{{ $candidature->statut }}</td>
                        <td>
                            <form action="{{ route('candidatures.updateSelection', $candidature) }}" method="POST">
                                @csrf
                                <select name="statut" onchange="this.form.submit()">
                                    <option value="en_attente" {{ $candidature->statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="accepte" {{ $candidature->statut == 'accepte' ? 'selected' : '' }}>Accepté</option>
                                    <option value="refuse" {{ $candidature->statut == 'refuse' ? 'selected' : '' }}>Refusé</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection