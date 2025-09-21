@extends('layouts.rh')

@section('title', 'Analyse des CV')

@section('content')
<div class="container">
    <h1>Analyse des CV</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5>Candidatures avec CV à analyser</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Candidat</th>
                        <th>Annonce</th>
                        <th>Statut CV</th>
                        <th>Score CV</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidatures as $candidature)
                        <tr>
                            <td>{{ $candidature->candidat->nom }} {{ $candidature->candidat->prenom }}</td>
                            <td>{{ $candidature->annonce->titre }}</td>
                            <td>
                                @if($candidature->cv)
                                    <span class="badge bg-success">Présent</span>
                                @else
                                    <span class="badge bg-warning">Absent</span>
                                @endif
                            </td>
                            <td>
                                @if($candidature->cvAnalyse)
                                    <span class="badge bg-info">{{ $candidature->cvAnalyse->score_competences ?? 0 }}/100</span>
                                @else
                                    <span class="badge bg-secondary">Non analysé</span>
                                @endif
                            </td>
                            <td>
                                @if($candidature->cv && !$candidature->cvAnalyse)
                                    <form action="{{ route('cvs.analyse', $candidature) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">🔍 Analyser</button>
                                    </form>
                                @elseif($candidature->cvAnalyse)
                                    <button class="btn btn-sm btn-success" disabled>Détails</button>
                                @else
                                    <span class="text-muted">Pas de CV</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucune candidature trouvée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection