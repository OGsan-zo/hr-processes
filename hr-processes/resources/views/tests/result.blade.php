@extends('layouts.rh')

@section('title', 'Résultat : ' . $test->titre)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center">
                <div class="card-header bg-{{ $resultat->pourcentage >= 80 ? 'success' : ($resultat->pourcentage >= 60 ? 'warning' : 'danger') }} text-white">
                    <h3>Résultat du test</h3>
                </div>
                <div class="card-body">
                    <h1 class="display-4 mb-4">{{ $resultat->score_obtenu }} / {{ $resultat->score_max }}</h1>
                    <h4 class="mb-4">{{ $resultat->note_formatted }}</h4>
                    
                    @if($resultat->pourcentage >= 80)
                        <div class="alert alert-success">
                            <h5>🎉 Excellent résultat !</h5>
                            <p>Vous avez réussi le test avec brio.</p>
                        </div>
                    @elseif($resultat->pourcentage >= 60)
                        <div class="alert alert-warning">
                            <h5>✅ Résultat satisfaisant</h5>
                            <p>Vous avez passé le test avec succès.</p>
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <h5>❌ Résultat insuffisant</h5>
                            <p>Vous pouvez repasser le test après révision.</p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('tests.index') }}" class="btn btn-primary me-2">← Retour aux tests</a>
                        <a href="{{ route('tests.pass', $test) }}" class="btn btn-secondary">🔄 Repasser le test</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection