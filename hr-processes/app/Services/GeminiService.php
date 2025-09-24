<?php

namespace App\Services;

use GeminiAPI\Gemini;
use GeminiAPI\Resources\Parts\TextPart;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $gemini;

    public function __construct()
    {
        // Initialiser le client Gemini avec la clé API
        $this->gemini = new Gemini(env('GEMINI_API_KEY'));
    }

    public function analyseCv($texteCv, $annonceDescription = '')
    {
        $modelName = 'gemini-1.5-flash';

        // Construire le prompt
        $prompt = "Analyse ce CV : \n\n$texteCv\n\n";
        $prompt .= "1. Extraire les compétences principales (liste séparée par virgules).\n";
        $prompt .= "2. Calculer score profil (0-100) basé sur âge, diplôme, expérience.\n";
        $prompt .= "3. Calculer score CV (0-100) basé sur motivation, expérience, compétences.\n";
        $prompt .= "4. Score global (moyenne des scores).\n";
        $prompt .= "Si description annonce fournie : '$annonceDescription', suggérer poste adapté.\n";
        $prompt .= "Répondre en JSON : { \"competences\": \"liste,sep,virgules\", \"score_profil\": 0, \"score_cv\": 0, \"score_global\": 0, \"poste_suggere\": \"suggestion poste\" }";

        try {
            // Appeler l'API Gemini
            $response = $this->gemini->generativeModel($modelName)->generateContent([
                new TextPart($prompt)
            ]);

            // Extraire le texte brut
            $generatedText = $response->text();

            // Nettoyer et parser JSON
            $generatedText = trim($generatedText, '`');
            if (stripos($generatedText, 'json') === 0) {
                $generatedText = substr($generatedText, strpos($generatedText, '{'));
            }

            $result = json_decode($generatedText, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from Gemini: ' . json_last_error_msg());
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Gemini API error: ' . $e->getMessage());
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