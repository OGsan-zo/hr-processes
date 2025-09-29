@extends('layouts.rh')

@section('title', 'Détails du Test : ' . $test->titre)

@section('content')
<div class="container">
    <h1>{{ $test->titre }}</h1>
    <p>Description: {{ $test->description }}</p>
    <p>Durée: {{ $test->duree_formatted }}</p>

    <h2>Questions</h2>
    <ul>
        @foreach($test->questions as $question)
            <li>
                <strong>Question: {{ $question->question }}</strong> (Points: {{ $question->points }})
                <ul>
                    @foreach($question->reponses as $reponse)
                        <li>{{ $reponse->reponse }} @if($reponse->correcte) (Correcte) @endif</li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
    <a href="{{ route('tests.index') }}" class="btn btn-secondary">Retour aux tests</a>
</div>
@endsection