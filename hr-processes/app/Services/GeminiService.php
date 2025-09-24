<?php

namespace App\Services;

use Google\Cloud\GenerativeAI;

class GeminiService
{
    protected $generativeAI;

    public function __construct()
    {
        // Initialize Gemini client with API key
        $this->generativeAI = new GenerativeAI(env('GEMINI_API_KEY'));
    }

    public function analyseCv($texteCv, $annonceDescription = '')
    {
        $modelName = 'gemini-1.5-flash';

        // Construct prompt
        $prompt = "Analyse ce CV : \n\n$texteCv\n\n";
        $prompt .= "1. Extraire les compétences principales (liste séparée par virgules).\n";
        $prompt .= "2. Calculer score profil (0-100) basé sur âge, diplôme, expérience.\n";
        $prompt .= "3. Calculer score CV (0-100) basé sur motivation, expérience, compétences.\n";
        $prompt .= "4. Score global (moyenne des scores).\n";
        $prompt .= "Si description annonce fournie : '$annonceDescription', suggérer poste adapté.\n";
        $prompt .= "Répondre en JSON : { \"competences\": \"liste,sep,virgules\", \"score_profil\": 0, \"score_cv\": 0, \"score_global\": 0, \"poste_suggere\": \"suggestion poste\" }";

        try {
            // Call Gemini API
            $response = $this->generativeAI->model($modelName)->generateContent($prompt);

            // Extract raw text
            $generatedText = $response->text();

            // Clean and parse JSON
            $generatedText = trim($generatedText, '`json');
            $generatedText = trim($generatedText, '`');

            return json_decode($generatedText, true);
        } catch (\Exception $e) {
            \Log::error('Gemini API error: ' . $e->getMessage());
            return [
                'competences' => '',
                'score_profil' => 0,
                'score_cv' => 0,
                'score_global' => 0,
                'poste_suggere' => null,
                'error' => 'Erreur API: ' . $e->getMessage()
            ];
        }
    }
}