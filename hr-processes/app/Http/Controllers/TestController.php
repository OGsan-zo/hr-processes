<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Question;
use App\Models\Reponse;
use Illuminate\Http\Request;

class TestController extends Controller
{
    private function checkPermission($permission)
    {
        if (!auth()->check() || !auth()->user()->can($permission)) {
            abort(403, 'Accès non autorisé.');
        }
    }

    public function index()
    {
        $this->checkPermission('manage-entretiens');
        $tests = Test::withCount('questions')->get();
        return view('tests.index', compact('tests'));
    }

    public function create()
    {
        $this->checkPermission('manage-entretiens');
        return view('tests.create');
    }

    public function store(Request $request)
    {
        $this->checkPermission('manage-entretiens');
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duree_minutes' => 'required|integer|min:5|max:120',
            'nombre_questions' => 'required|integer|min:1|max:50',
        ]);

        $test = Test::create($request->all());
        return redirect()->route('tests.index')->with('success', 'Test créé avec succès.');
    }

    public function show(Test $test)
    {
        $this->checkPermission('manage-entretiens');
        $test->load(['questions.reponses']);
        return view('tests.show', compact('test'));
    }

    public function edit(Test $test)
    {
        $this->checkPermission('manage-entretiens');
        return view('tests.edit', compact('test'));
    }

    public function update(Request $request, Test $test)
    {
        $this->checkPermission('manage-entretiens');
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duree_minutes' => 'required|integer|min:5|max:120',
            'nombre_questions' => 'required|integer|min:1|max:50',
            'statut' => 'required|in:actif,inactif',
        ]);

        $test->update($request->all());
        return redirect()->route('tests.index')->with('success', 'Test mis à jour.');
    }

    public function destroy(Test $test)
    {
        $this->checkPermission('manage-entretiens');
        $test->delete();
        return redirect()->route('tests.index')->with('success', 'Test supprimé.');
    }

    // Ajouter question
    public function addQuestion(Request $request, Test $test)
    {
        $this->checkPermission('manage-entretiens');
        $request->validate([
            'question' => 'required|string|max:1000',
            'points' => 'required|integer|min:1|max:10',
            'type' => 'required|in:qcm,ouverte',
        ]);

        $question = Question::create([
            'test_id' => $test->id,
            'question' => $request->question,
            'points' => $request->points,
            'type' => $request->type,
        ]);

        if ($request->type === 'qcm') {
            foreach ($request->reponses as $index => $reponse) {
                if (!empty($reponse['texte'])) {
                    Reponse::create([
                        'question_id' => $question->id,
                        'reponse' => $reponse['texte'],
                        'correcte' => isset($reponse['correcte']),
                    ]);
                }
            }
        }

        return redirect()->route('tests.show', $test)->with('success', 'Question ajoutée.');
    }
}