<?php

namespace App\Services;

use Google\Client;
use Google\Service\GenerativeLanguage;

class GeminiService
{
    protected $service;

    public function __construct()
    {
        $client = new Client();
        $client->setApplicationName('RH Project');
        $client->setDeveloperKey(env('GEMINI_API_KEY'));
        $this->service = new GenerativeLanguage($client);
    }

    public function analyseCv($texteCv, $annonceDescription = '')
    {
        $modelName = 'models/gemini-1.5-flash';

        $prompt = "Analyse ce CV : \n\n$texteCv\n\n";
        $prompt .= "1. Extraire les compétences principales (liste séparée par virgules).\n";
        $prompt .= "2. Calculer score profil (0-100) basé sur âge, diplôme, expérience.\n";
        $prompt .= "3. Calculer score CV (0-100) basé sur motivation, expérience, compétences.\n";
        $prompt .= "4. Score global (moyenne des scores).\n";
        $prompt .= "Si description annonce fournie : '$annonceDescription', suggérer poste adapté.\n";
        $prompt .= "Répondre en JSON : { \"competences\": \"liste,sep,virgules\", \"score_profil\": 0, \"score_cv\": 0, \"score_global\": 0, \"poste_suggere\": \"suggestion poste\" }";

        $response = $this->service->models->generateContent($modelName, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        $generatedText = $response->getCandidates()[0]->getContent()->getParts()[0]->getText();

        // Nettoyer et parser JSON
        $generatedText = trim($generatedText, '`json');
        $generatedText = trim($generatedText, '`');

        return json_decode($generatedText, true);
    }
}