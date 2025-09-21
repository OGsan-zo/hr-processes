@extends('layouts.rh')

@section('title', 'Tests QCM')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Tests QCM</h1>
        <a href="{{ route('tests.create') }}" class="btn btn-primary">➕ Nouveau test</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Durée</th>
                        <th>Questions</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tests as $test)
                        <tr>
                            <td>{{ $test->titre }}</td>
                            <td>{{ $test->duree_formatted }}</td>
                            <td>{{ $test->questions_count }}</td>
                            <td>
                                <span class="badge bg-{{ $test->statut == 'actif' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($test->statut) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('tests.show', $test) }}" class="btn btn-sm btn-info">Voir</a>
                                <a href="{{ route('tests.edit', $test) }}" class="btn btn-sm btn-warning">Modifier</a>
                                <form action="{{ route('tests.destroy', $test) }}" method="POST" style="display: inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection