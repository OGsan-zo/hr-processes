<?php

namespace App\Services;

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected Client $client;
    protected string $modelName;
    
    // Configuration du service
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY_MS = 1000;
    
    // Scores par défaut
    private const DEFAULT_SCORE = 0;
    private const MIN_SCORE = 0;
    private const MAX_SCORE = 100;
    
    // QCM Configuration
    private const QCM_COUNT = 10;
    private const QCM_OPTIONS_COUNT = 4;
    private const MIN_QCM_OPTIONS = 2;

    public function __construct()
    {
        $apiKey = env('GEMINI_API_KEY');
        
        if (empty($apiKey)) {
            Log::error('❌ GEMINI_API_KEY non configurée dans .env');
            throw new \RuntimeException('GEMINI_API_KEY est requise');
        }
        
        $this->client = new Client($apiKey);
        $this->modelName = env('GEMINI_MODEL', 'gemini-2.5-flash');
    }

    /**
     * Analyse un CV avec ou sans description d'annonce
     * 
     * @param string $texteCv Le texte extrait du CV
     * @param string $annonceDescription Description optionnelle du poste
     * @return array Résultats de l'analyse avec scores et compétences
     */
    public function analyseCv(string $texteCv, string $annonceDescription = ''): array
    {
        $prompt = $this->buildCvAnalysisPrompt($texteCv, $annonceDescription);
        
        try {
            $response = $this->callGeminiAPI($prompt);
            $cleanedJson = $this->cleanJsonResponse($response);
            $result = $this->parseJson($cleanedJson, 'CV Analysis');
            
            return $this->validateCvAnalysisResult($result);
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de l\'analyse CV', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->getDefaultCvResult($e->getMessage());
        }
    }

    /**
     * Génère des questions QCM basées sur une description d'annonce
     * 
     * @param string $annonceDescription Description du poste
     * @param int $count Nombre de questions à générer
     * @return array Liste des QCM générés
     */
    public function generateQCM(string $annonceDescription, int $count = self::QCM_COUNT): array
    {
        if (empty(trim($annonceDescription))) {
            Log::warning('⚠️ Description d\'annonce vide pour la génération de QCM');
            return [];
        }

        $prompt = $this->buildQcmGenerationPrompt($annonceDescription, $count);
        
        try {
            $response = $this->callGeminiAPI($prompt, maxRetries: self::MAX_RETRIES);
            $cleanedJson = $this->cleanJsonResponse($response);
            $qcms = $this->parseJson($cleanedJson, 'QCM Generation');
            
            if (!is_array($qcms)) {
                Log::error('❌ La réponse QCM n\'est pas un tableau', [
                    'type' => gettype($qcms)
                ]);
                return [];
            }
            
            return $this->validateAndFilterQcms($qcms, $count);
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de la génération des QCM', [
                'error' => $e->getMessage(),
                'description_length' => strlen($annonceDescription)
            ]);
            
            return [];
        }
    }

    /**
     * Analyse simple en cas d'échec de l'API
     * 
     * @param string $texteCv
     * @return array
     */
    public function analyseSimple(string $texteCv): array
    {
        $competences = $this->extraireCompetencesBasique($texteCv);
        $score = $this->calculerScoreBasique($texteCv);

        return [
            'competences' => implode(', ', $competences),
            'score_profil' => $score,
            'score_cv' => $score,
            'score_global' => $score,
            'poste_suggere' => $this->suggererPosteBasique($competences)
        ];
    }

    // ============================================================
    // MÉTHODES PRIVÉES - Construction des prompts
    // ============================================================

    /**
     * Construit le prompt pour l'analyse CV
     */
    private function buildCvAnalysisPrompt(string $texteCv, string $annonceDescription): string
    {
        $prompt = "Analyse ce CV et réponds UNIQUEMENT avec un objet JSON valide (sans markdown, sans balises, sans texte supplémentaire).\n\n";
        $prompt .= "CV à analyser :\n" . substr($texteCv, 0, 8000) . "\n\n";
        
        if (!empty($annonceDescription)) {
            $prompt .= "Description du poste :\n" . substr($annonceDescription, 0, 2000) . "\n\n";
        }
        
        $prompt .= "Instructions :\n";
        $prompt .= "1. Extraire les compétences principales du CV (séparées par des virgules)\n";
        $prompt .= "2. Calculer un score_profil (0-100) basé sur : âge, diplôme, expérience totale\n";
        $prompt .= "3. Calculer un score_cv (0-100) basé sur : qualité du CV, motivation apparente, compétences\n";
        $prompt .= "4. Calculer le score_global (moyenne des deux scores)\n";
        
        if (!empty($annonceDescription)) {
            $prompt .= "5. Suggérer un poste adapté en fonction de la description fournie\n\n";
        } else {
            $prompt .= "5. Suggérer un poste adapté en fonction des compétences identifiées\n\n";
        }
        
        $prompt .= "Format de réponse OBLIGATOIRE (JSON pur, sans ```json) :\n";
        $prompt .= json_encode([
            'competences' => 'PHP, Laravel, JavaScript, React',
            'score_profil' => 85,
            'score_cv' => 78,
            'score_global' => 82,
            'poste_suggere' => 'Développeur Full Stack'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return $prompt;
    }

    /**
     * Construit le prompt pour la génération de QCM
     */
    private function buildQcmGenerationPrompt(string $annonceDescription, int $count): string
    {
        $prompt = "Tu es un expert en recrutement RH. Génère EXACTEMENT {$count} questions QCM pour évaluer les candidats.\n\n";
        $prompt .= "Description du poste :\n" . substr($annonceDescription, 0, 2000) . "\n\n";
        
        $prompt .= "RÈGLES STRICTES :\n";
        $prompt .= "1. Génère EXACTEMENT {$count} questions (ni plus, ni moins)\n";
        $prompt .= "2. Chaque question doit avoir EXACTEMENT 4 options (A, B, C, D)\n";
        $prompt .= "3. Indique l'index de la réponse correcte (0=A, 1=B, 2=C, 3=D)\n";
        $prompt .= "4. Questions variées : compétences techniques (60%), soft skills (20%), situations pratiques (20%)\n";
        $prompt .= "5. Questions pertinentes et réalistes pour le poste décrit\n";
        $prompt .= "6. Évite les questions trop faciles ou trop difficiles\n\n";
        
        $prompt .= "Format de réponse OBLIGATOIRE (tableau JSON pur, sans ```json) :\n";
        $prompt .= json_encode([
            [
                'question' => 'Quelle est la meilleure pratique pour sécuriser une application Laravel ?',
                'options' => [
                    'Utiliser des requêtes SQL brutes',
                    'Valider toutes les entrées utilisateur',
                    'Désactiver CSRF protection',
                    'Stocker les mots de passe en clair'
                ],
                'correct_index' => 1
            ],
            [
                'question' => 'Comment gérez-vous un conflit avec un collègue ?',
                'options' => [
                    'J\'ignore le problème',
                    'J\'en parle calmement en privé',
                    'Je démissionne',
                    'Je le signale immédiatement'
                ],
                'correct_index' => 1
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return $prompt;
    }

    // ============================================================
    // MÉTHODES PRIVÉES - Appels API et parsing
    // ============================================================

    /**
     * Appel à l'API Gemini avec retry automatique
     */
    private function callGeminiAPI(string $prompt, int $maxRetries = 1): string
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxRetries) {
            try {
                $response = $this->client
                    ->generativeModel($this->modelName)
                    ->generateContent(new TextPart($prompt));
                
                $text = $response->text();
                
                if (empty($text)) {
                    throw new \RuntimeException('Réponse vide de l\'API Gemini');
                }
                
                Log::info('✅ Réponse Gemini reçue', [
                    'attempt' => $attempt + 1,
                    'length' => strlen($text),
                    'preview' => substr($text, 0, 200)
                ]);
                
                return $text;
                
            } catch (\Exception $e) {
                $lastException = $e;
                $attempt++;
                
                Log::warning("⚠️ Tentative {$attempt}/{$maxRetries} échouée", [
                    'error' => $e->getMessage()
                ]);
                
                if ($attempt < $maxRetries) {
                    usleep(self::RETRY_DELAY_MS * 1000 * $attempt); // Backoff exponentiel
                }
            }
        }

        throw new \RuntimeException(
            "Échec après {$maxRetries} tentatives: " . $lastException->getMessage(),
            0,
            $lastException
        );
    }

    /**
     * Nettoie la réponse pour extraire le JSON pur
     */
    private function cleanJsonResponse(string $response): string
    {
        $response = trim($response);
        
        // Supprimer les balises markdown
        $response = preg_replace('/^```(?:json)?\s*/im', '', $response);
        $response = preg_replace('/\s*```$/m', '', $response);
        
        // Trouver le premier caractère JSON significatif
        if (preg_match('/[\[{]/', $response, $matches, PREG_OFFSET_CAPTURE)) {
            $startPos = $matches[0][1];
            $firstChar = $matches[0][0];
            
            // Extraire du début jusqu'à la fin du JSON
            $response = substr($response, $startPos);
            
            // Trouver le dernier caractère correspondant
            $endChar = ($firstChar === '{') ? '}' : ']';
            $lastPos = strrpos($response, $endChar);
            
            if ($lastPos !== false) {
                $response = substr($response, 0, $lastPos + 1);
            }
        }
        
        // Nettoyer les caractères invisibles
        $response = preg_replace('/[\x00-\x1F\x7F]/u', '', $response);
        
        return trim($response);
    }

    /**
     * Parse le JSON avec gestion d'erreurs
     */
    private function parseJson(string $json, string $context = ''): mixed
    {
        $result = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('❌ Erreur parsing JSON', [
                'context' => $context,
                'error' => json_last_error_msg(),
                'json_preview' => substr($json, 0, 500)
            ]);
            
            throw new \RuntimeException(
                "Erreur JSON ({$context}): " . json_last_error_msg()
            );
        }
        
        return $result;
    }

    // ============================================================
    // MÉTHODES PRIVÉES - Validation et transformation
    // ============================================================

    /**
     * Valide et normalise le résultat d'analyse CV
     */
    private function validateCvAnalysisResult(array $result): array
    {
        $defaults = [
            'competences' => '',
            'score_profil' => self::DEFAULT_SCORE,
            'score_cv' => self::DEFAULT_SCORE,
            'score_global' => self::DEFAULT_SCORE,
            'poste_suggere' => null
        ];

        $result = array_merge($defaults, $result);

        // Normaliser les scores
        $result['score_profil'] = $this->normalizeScore($result['score_profil']);
        $result['score_cv'] = $this->normalizeScore($result['score_cv']);
        $result['score_global'] = $this->normalizeScore($result['score_global']);

        // Recalculer le score global si incohérent
        $expectedGlobal = round(($result['score_profil'] + $result['score_cv']) / 2);
        if (abs($result['score_global'] - $expectedGlobal) > 5) {
            $result['score_global'] = $expectedGlobal;
            Log::info('ℹ️ Score global recalculé', [
                'ancien' => $result['score_global'],
                'nouveau' => $expectedGlobal
            ]);
        }

        // Nettoyer les chaînes
        $result['competences'] = $this->cleanString($result['competences']);
        $result['poste_suggere'] = $this->cleanString($result['poste_suggere']);

        return $result;
    }

    /**
     * Valide et filtre les QCM générés
     */
    private function validateAndFilterQcms(array $qcms, int $expectedCount): array
    {
        $validQcms = [];

        foreach ($qcms as $index => $qcm) {
            if ($this->isValidQcm($qcm)) {
                $validQcms[] = $this->normalizeQcm($qcm);
            } else {
                Log::warning("⚠️ QCM #{$index} invalide ignoré", [
                    'qcm' => $qcm
                ]);
            }
        }

        $count = count($validQcms);

        if ($count === 0) {
            Log::error('❌ Aucun QCM valide généré');
            return [];
        }

        // Ajuster au nombre attendu
        if ($count < $expectedCount) {
            Log::warning("⚠️ Seulement {$count}/{$expectedCount} QCM valides générés");
        } elseif ($count > $expectedCount) {
            Log::info("✂️ {$count} QCM générés, on garde les {$expectedCount} premiers");
            $validQcms = array_slice($validQcms, 0, $expectedCount);
        } else {
            Log::info("✅ {$count} QCM valides générés avec succès");
        }

        return $validQcms;
    }

    /**
     * Vérifie si un QCM est valide
     */
    private function isValidQcm(mixed $qcm): bool
    {
        if (!is_array($qcm)) {
            return false;
        }

        // Vérifier les clés obligatoires
        $requiredKeys = ['question', 'options', 'correct_index'];
        foreach ($requiredKeys as $key) {
            if (!isset($qcm[$key])) {
                return false;
            }
        }

        // Valider la question
        if (!is_string($qcm['question']) || strlen(trim($qcm['question'])) < 10) {
            return false;
        }

        // Valider les options
        if (!is_array($qcm['options']) || count($qcm['options']) < self::MIN_QCM_OPTIONS) {
            return false;
        }

        foreach ($qcm['options'] as $option) {
            if (!is_string($option) || strlen(trim($option)) < 2) {
                return false;
            }
        }

        // Valider l'index de la réponse correcte
        if (!is_numeric($qcm['correct_index'])) {
            return false;
        }

        $correctIndex = (int)$qcm['correct_index'];
        if ($correctIndex < 0 || $correctIndex >= count($qcm['options'])) {
            return false;
        }

        return true;
    }

    /**
     * Normalise un QCM (nettoie et formate)
     */
    private function normalizeQcm(array $qcm): array
    {
        return [
            'question' => trim($qcm['question']),
            'options' => array_map('trim', $qcm['options']),
            'correct_index' => (int)$qcm['correct_index']
        ];
    }

    // ============================================================
    // MÉTHODES PRIVÉES - Utilitaires
    // ============================================================

    /**
     * Normalise un score entre MIN_SCORE et MAX_SCORE
     */
    private function normalizeScore(mixed $score): int
    {
        $score = is_numeric($score) ? (int)$score : self::DEFAULT_SCORE;
        return max(self::MIN_SCORE, min(self::MAX_SCORE, $score));
    }

    /**
     * Nettoie une chaîne de caractères
     */
    private function cleanString(?string $str): string
    {
        return $str ? trim($str) : '';
    }

    /**
     * Retourne un résultat par défaut en cas d'erreur
     */
    private function getDefaultCvResult(string $errorMessage): array
    {
        return [
            'competences' => '',
            'score_profil' => self::DEFAULT_SCORE,
            'score_cv' => self::DEFAULT_SCORE,
            'score_global' => self::DEFAULT_SCORE,
            'poste_suggere' => null,
            'error' => 'Erreur API: ' . $errorMessage
        ];
    }

    // ============================================================
    // MÉTHODES PRIVÉES - Analyse basique (fallback)
    // ============================================================

    private function extraireCompetencesBasique(string $texte): array
    {
        $motsCles = [
            'PHP', 'Laravel', 'Symfony', 'JavaScript', 'TypeScript', 'React', 'Vue', 'Angular',
            'Node.js', 'Python', 'Django', 'Java', 'Spring', 'C#', '.NET',
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'Docker', 'Kubernetes',
            'Git', 'AWS', 'Azure', 'GCP', 'HTML', 'CSS', 'Bootstrap', 'Tailwind'
        ];
        
        $competences = [];
        $texteMinuscule = strtolower($texte);
        
        foreach ($motsCles as $mot) {
            if (stripos($texteMinuscule, strtolower($mot)) !== false) {
                $competences[] = $mot;
            }
        }
        
        return array_unique($competences);
    }

    private function calculerScoreBasique(string $texte): int
    {
        $score = 50; // Score de base
        $longueur = strlen($texte);
        
        // Bonus pour la longueur
        if ($longueur > 2000) $score += 10;
        if ($longueur > 4000) $score += 10;
        if ($longueur > 6000) $score += 5;
        
        // Bonus pour mots-clés importants
        $motsClesImportants = [
            'expérience' => 5,
            'projet' => 5,
            'développement' => 5,
            'gestion' => 3,
            'équipe' => 3,
            'leadership' => 4,
            'responsabilité' => 3
        ];
        
        foreach ($motsClesImportants as $mot => $points) {
            if (stripos($texte, $mot) !== false) {
                $score += $points;
            }
        }
        
        return min(self::MAX_SCORE, $score);
    }

    private function suggererPosteBasique(array $competences): string
    {
        $competencesStr = implode(',', array_map('strtolower', $competences));
        
        if (str_contains($competencesStr, 'laravel') || str_contains($competencesStr, 'php')) {
            return 'Développeur PHP/Laravel';
        }
        
        if (str_contains($competencesStr, 'react') || str_contains($competencesStr, 'vue') || str_contains($competencesStr, 'angular')) {
            return 'Développeur Frontend';
        }
        
        if (str_contains($competencesStr, 'python') || str_contains($competencesStr, 'django')) {
            return 'Développeur Python';
        }
        
        if (str_contains($competencesStr, 'java') || str_contains($competencesStr, 'spring')) {
            return 'Développeur Java';
        }
        
        if (str_contains($competencesStr, 'docker') || str_contains($competencesStr, 'kubernetes')) {
            return 'DevOps Engineer';
        }
        
        return 'Développeur Junior';
    }
}