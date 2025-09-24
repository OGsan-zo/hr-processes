<?php

namespace App\Services;

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $client;

    public function __construct()
    {
        // Initialize Gemini client with API key
        $this->client = new Client(env('GEMINI_API_KEY'));
    }

    public function analyseCv($texteCv, $annonceDescription = '')
    {
        $modelName = 'gemini-1.5-flash';

        // Prompt amélioré avec des instructions plus claires
        $prompt = "Analyse ce CV et réponds UNIQUEMENT avec un objet JSON valide, sans markdown, sans balises, sans texte supplémentaire :\n\n";
        $prompt .= "CV à analyser :\n$texteCv\n\n";
        
        if (!empty($annonceDescription)) {
            $prompt .= "Description du poste :\n$annonceDescription\n\n";
        }
        
        $prompt .= "Instructions :\n";
        $prompt .= "1. Extraire les compétences principales du CV\n";
        $prompt .= "2. Calculer un score profil (0-100) basé sur l'âge, diplôme, expérience\n";
        $prompt .= "3. Calculer un score CV (0-100) basé sur la motivation, expérience, compétences\n";
        $prompt .= "4. Calculer le score global (moyenne des deux scores)\n";
        $prompt .= "5. Si une description de poste est fournie, suggérer un poste adapté\n\n";
        
        $prompt .= "IMPORTANT : Réponds UNIQUEMENT avec ce JSON (pas de texte avant ou après) :\n";
        $prompt .= '{"competences": "liste des compétences séparées par virgules", "score_profil": 85, "score_cv": 78, "score_global": 82, "poste_suggere": "nom du poste suggéré"}';

        try {
            // Call Gemini API
            $response = $this->client->generativeModel($modelName)->generateContent(new TextPart($prompt));
            $generatedText = $response->text();

            Log::info('Réponse brute de Gemini: ' . $generatedText);

            // Nettoyer la réponse
            $cleanedJson = $this->cleanGeminiResponse($generatedText);
            Log::info('JSON nettoyé: ' . $cleanedJson);

            // Parser le JSON
            $result = json_decode($cleanedJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Erreur JSON: ' . json_last_error_msg());
                Log::error('Contenu JSON problématique: ' . $cleanedJson);
                throw new \Exception('Invalid JSON response from Gemini: ' . json_last_error_msg());
            }

            // Valider et nettoyer les données
            $result = $this->validateAndCleanResult($result);

            return $result;

        } catch (\Exception $e) {
            Log::error('Gemini API error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
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

    /**
     * Nettoie la réponse de Gemini pour extraire uniquement le JSON
     */
    private function cleanGeminiResponse($response)
    {
        // Supprimer les espaces en début et fin
        $response = trim($response);
        
        // Supprimer les balises markdown communes
        $response = preg_replace('/^```json\s*/i', '', $response);
        $response = preg_replace('/\s*```$/', '', $response);
        $response = preg_replace('/^```\s*/', '', $response);
        
        // Supprimer tout ce qui précède la première accolade ouvrante
        $firstBrace = strpos($response, '{');
        if ($firstBrace !== false) {
            $response = substr($response, $firstBrace);
        }
        
        // Supprimer tout ce qui suit la dernière accolade fermante
        $lastBrace = strrpos($response, '}');
        if ($lastBrace !== false) {
            $response = substr($response, 0, $lastBrace + 1);
        }
        
        // Nettoyer les caractères invisibles et les retours à la ligne problématiques
        $response = preg_replace('/[\x00-\x1F\x7F]/', '', $response);
        
        return trim($response);
    }

    /**
     * Valide et nettoie le résultat parsé
     */
    private function validateAndCleanResult($result)
    {
        if (!is_array($result)) {
            throw new \Exception('Le résultat n\'est pas un tableau valide');
        }

        // Valeurs par défaut
        $defaults = [
            'competences' => '',
            'score_profil' => 0,
            'score_cv' => 0,
            'score_global' => 0,
            'poste_suggere' => null
        ];

        // Fusionner avec les valeurs par défaut
        $result = array_merge($defaults, $result);

        // Valider et corriger les scores (doivent être entre 0 et 100)
        $result['score_profil'] = max(0, min(100, (int)$result['score_profil']));
        $result['score_cv'] = max(0, min(100, (int)$result['score_cv']));
        $result['score_global'] = max(0, min(100, (int)$result['score_global']));

        // Si le score global n'est pas cohérent, le recalculer
        $expectedGlobal = round(($result['score_profil'] + $result['score_cv']) / 2);
        if (abs($result['score_global'] - $expectedGlobal) > 5) {
            $result['score_global'] = $expectedGlobal;
        }

        // Nettoyer les compétences
        if (!empty($result['competences'])) {
            $result['competences'] = trim($result['competences']);
        }

        // Nettoyer le poste suggéré
        if (!empty($result['poste_suggere'])) {
            $result['poste_suggere'] = trim($result['poste_suggere']);
        }

        return $result;
    }

    /**
     * Méthode de fallback si l'analyse automatique échoue
     */
    public function analyseSimple($texteCv)
    {
        // Analyse basique basée sur des mots-clés
        $competences = $this->extraireCompetencesBasique($texteCv);
        $scoreEstime = $this->calculerScoreBasique($texteCv);

        return [
            'competences' => implode(', ', $competences),
            'score_profil' => $scoreEstime,
            'score_cv' => $scoreEstime,
            'score_global' => $scoreEstime,
            'poste_suggere' => $this->suggererPosteBasique($competences)
        ];
    }

    private function extraireCompetencesBasique($texte)
    {
        $motsCles = [
            'php', 'laravel', 'javascript', 'react', 'mysql', 'postgresql', 
            'docker', 'git', 'aws', 'python', 'java', 'html', 'css', 
            'bootstrap', 'vue', 'angular', 'symfony', 'node'
        ];
        
        $competences = [];
        $texteMinuscule = strtolower($texte);
        
        foreach ($motsCles as $mot) {
            if (strpos($texteMinuscule, $mot) !== false) {
                $competences[] = ucfirst($mot);
            }
        }
        
        return array_unique($competences);
    }

    private function calculerScoreBasique($texte)
    {
        $score = 50; // Score de base
        
        // Bonus pour la longueur du CV
        if (strlen($texte) > 2000) $score += 10;
        if (strlen($texte) > 4000) $score += 10;
        
        // Bonus pour certains mots-clés
        $motsClesImportants = ['expérience', 'projet', 'développement', 'gestion'];
        foreach ($motsClesImportants as $mot) {
            if (stripos($texte, $mot) !== false) {
                $score += 5;
            }
        }
        
        return min(100, $score);
    }

    private function suggererPosteBasique($competences)
    {
        if (in_array('Laravel', $competences) || in_array('PHP', $competences)) {
            return 'Développeur PHP/Laravel';
        } elseif (in_array('React', $competences) || in_array('Javascript', $competences)) {
            return 'Développeur Frontend';
        } elseif (in_array('Python', $competences)) {
            return 'Développeur Python';
        }
        
        return 'Développeur Junior';
    }
}