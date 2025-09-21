@extends('layouts.rh')

@section('title', 'Passer le test : ' . $test->titre)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ $test->titre }}</h4>
                    <span class="badge bg-info">{{ $test->duree_formatted }}</span>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $test->description }}</p>
                    
                    <form action="{{ route('tests.pass', $test) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <h5>Instructions :</h5>
                            <ul>
                                <li>Durée : {{ $test->duree_formatted }}</li>
                                <li>Questions : {{ $test->nombre_questions }}</li>
                                <li>Points maximum : {{ $test->questions->sum('points') }}</li>
                            </ul>
                        </div>

                        @foreach($test->questions as $index => $question)
                            <div class="mb-4 p-3 border rounded">
                                <h6 class="mb-3">Question {{ $index + 1 }} ({{ $question->points }} pts)</h6>
                                <p class="mb-3">{{ $question->question }}</p>
                                
                                @if($question->type === 'qcm')
                                    @foreach($question->reponses as $reponse)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" 
                                                   name="question_{{ $question->id }}" 
                                                   value="{{ $reponse->id }}"
                                                   id="q{{ $question->id }}_r{{ $reponse->id }}">
                                            <label class="form-check-label" for="q{{ $question->id }}_r{{ $reponse->id }}">
                                                {{ $reponse->reponse }}
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <textarea name="question_{{ $question->id }}" class="form-control" rows="3" 
                                            placeholder="Votre réponse..."></textarea>
                                @endif
                            </div>
                        @endforeach

                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg">📝 Soumettre le test</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection