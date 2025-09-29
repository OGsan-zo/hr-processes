<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Test;
use App\Models\Question;
use App\Models\Reponse;
use Illuminate\Http\Request;
use App\Services\GeminiService;

class AnnonceController extends Controller
{
    public function index()
    {
        $annonces = Annonce::all();
        return view('annonces.index', compact('annonces'));
    }

    public function create()
    {
        return view('annonces.create');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:150',
            'description' => 'required|string',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
        ]);

        $annonce = Annonce::create($request->all());

            // Génération automatique des QCM via Gemini
        $geminiService = app(GeminiService::class);  // Résolvez le service
        $qcms = $geminiService->generateQCM($annonce->description);

        if (!empty($qcms)) {
            // Créer le Test associé
            $test = Test::create([
                'titre' => 'Test pour ' . $annonce->titre,
                'description' => 'QCM généré automatiquement pour cette annonce',
                'duree_minutes' => 30,  // Durée par défaut
                'nombre_questions' => 10,
                'statut' => 'actif',
                'annonce_id' => $annonce->id,
            ]);

            // Ajouter les questions et réponses
            foreach ($qcms as $qcm) {
                $question = Question::create([
                    'test_id' => $test->id,
                    'question' => $qcm['question'],
                    'points' => 1,  // Points par question
                    'type' => 'qcm',
                ]);

                foreach ($qcm['options'] as $index => $option) {
                    Reponse::create([
                        'question_id' => $question->id,
                        'reponse' => $option,
                        'correcte' => ($index === (int)$qcm['correct_index']),
                    ]);
                }
            }
        }

        return redirect()->route('annonces.index')->with('success', 'Annonce créée avec succès. ' . (empty($qcms) ? ' (QCM non générés en raison d\'une erreur API)' : '10 QCM générés et stockés.'));
    }


    public function edit(Annonce $annonce)
    {
        return view('annonces.edit', compact('annonce'));
    }

    public function update(Request $request, Annonce $annonce)
    {
        $request->validate([
            'titre' => 'required|string|max:150',
            'description' => 'required|string',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'in:active,inactive,expiree',
        ]);

        $annonce->update($request->all());

        return redirect()->route('annonces.index')->with('success', 'Annonce mise à jour.');
    }

    public function destroy(Annonce $annonce)
    {
        if ($annonce->test) {
            $annonce->test->delete();  // Supprime le test et ses questions/réponses en cascade (si configuré)
        }
        $annonce->delete();
        return redirect()->route('annonces.index')->with('success', 'Annonce supprimée.');
    }

}
