<?php

namespace Uiaciel\SuryaCms\Services;

class KeywordExtractionService
{
    /**
     * Stop words dalam bahasa Indonesia dan Inggris
     * Words yang tidak relevan sebagai keyword
     */
    private const STOP_WORDS = [
        // Bahasa Indonesia
        'yang', 'dan', 'di', 'ke', 'dari', 'untuk', 'pada', 'atau', 'ini', 'itu',
        'dengan', 'ada', 'adalah', 'tidak', 'telah', 'akan', 'dapat', 'oleh', 'lebih',
        'saat', 'jika', 'hanya', 'seperti', 'dalam', 'juga', 'maka', 'setelah', 'sampai',
        'semua', 'satu', 'apa', 'mengapa', 'bagaimana', 'dimana', 'kapan', 'siapa',
        'sudah', 'pernah', 'sedang', 'bisa', 'harus', 'mereka', 'kami', 'kita', 'saya',
        'anda', 'dia', 'dia', 'kami', 'kalian', 'perlu', 'beberapa', 'banyak', 'sangat',
        'cukup', 'terlalu', 'agak', 'paling', 'amat', 'karena', 'sebab', 'meski', 'meskipun',
        'padahal', 'namun', 'namun', 'walaupun', 'sebaliknya', 'malah', 'justru',
        // Bahasa Inggris
        'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with',
        'by', 'from', 'is', 'are', 'was', 'were', 'been', 'be', 'have', 'has', 'had',
        'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'must',
        'can', 'that', 'this', 'these', 'those', 'i', 'you', 'he', 'she', 'it', 'we',
        'they', 'what', 'which', 'who', 'whom', 'where', 'when', 'why', 'how', 'all',
        'each', 'every', 'both', 'few', 'more', 'most', 'other', 'some', 'such', 'no',
        'nor', 'not', 'only', 'same', 'so', 'than', 'too', 'very', 'just', 'if', 'as',
    ];

    /**
     * Extract keywords dari text
     *
     * @param string $text Text untuk di-extract keywordnya
     * @param int $count Jumlah keyword yang diinginkan (default: 10)
     * @return array Array of keywords
     */
    public function extract(string $text, int $count = 10): array
    {
        // Clean dan normalize text
        $cleanText = $this->cleanText($text);

        // Split menjadi words
        $words = $this->tokenize($cleanText);

        // Filter stop words dan short words
        $filteredWords = $this->filterWords($words);

        // Calculate word frequency
        $wordFrequency = $this->calculateFrequency($filteredWords);

        // Sort by frequency dan ambil top keywords
        arsort($wordFrequency);
        $topKeywords = array_slice(array_keys($wordFrequency), 0, $count);

        // Sort hasil alfabetikal untuk consistency
        sort($topKeywords);

        return $topKeywords;
    }

    /**
     * Extract keyphrases (multi-word keywords) dari text
     *
     * @param string $text Text untuk di-extract keywordnya
     * @param int $phraseLength Panjang phrase (default: 2-3 words)
     * @param int $count Jumlah phrases yang diinginkan (default: 10)
     * @return array Array of keyphrases
     */
    public function extractPhrases(string $text, int $phraseLength = 2, int $count = 10): array
    {
        $cleanText = $this->cleanText($text);
        $words = $this->tokenize($cleanText);
        $filteredWords = $this->filterWords($words);

        $phrases = [];

        // Generate n-grams (phrases)
        for ($i = 0; $i < count($filteredWords) - $phraseLength + 1; $i++) {
            $phrase = implode(' ', array_slice($filteredWords, $i, $phraseLength));

            // Check jika phrase sudah ada
            if (isset($phrases[$phrase])) {
                $phrases[$phrase]++;
            } else {
                $phrases[$phrase] = 1;
            }
        }

        // Filter phrases yang hanya muncul lebih dari 1 kali
        $phrases = array_filter($phrases, fn($count) => $count > 1);

        // Sort by frequency
        arsort($phrases);

        // Get top phrases
        $topPhrases = array_slice(array_keys($phrases), 0, $count);

        return $topPhrases;
    }

    /**
     * Generate keywords format untuk tags field (comma-separated)
     *
     * @param string $title Judul artikel
     * @param string $content Konten artikel
     * @param int $keywordCount Jumlah single keywords
     * @param int $phraseCount Jumlah multi-word phrases
     * @return string Keywords separated by commas
     */
    public function generateTags(string $title, string $content, int $keywordCount = 5, int $phraseCount = 3): string
    {
        $keywords = [];

        // Extract keywords dari title (lebih prioritas)
        $titleKeywords = $this->extract($title, 3);
        $keywords = array_merge($keywords, $titleKeywords);

        // Extract keywords dari content
        $contentKeywords = $this->extract($content, $keywordCount);
        $keywords = array_merge($keywords, $contentKeywords);

        // Extract phrases dari content
        $phrases = $this->extractPhrases($content, 2, $phraseCount);
        $keywords = array_merge($keywords, $phrases);

        // Remove duplicates dan combine
        $keywords = array_unique($keywords);

        // Limit hasil
        $keywords = array_slice($keywords, 0, $keywordCount + $phraseCount);

        return implode(', ', $keywords);
    }

    /**
     * Clean text dari HTML tags dan special characters
     *
     * @param string $text
     * @return string
     */
    private function cleanText(string $text): string
    {
        // Remove HTML tags
        $text = strip_tags($text);

        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        // Convert to lowercase
        $text = mb_strtolower($text, 'UTF-8');

        // Remove punctuation but keep hyphens and underscores
        $text = preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $text);

        return trim($text);
    }

    /**
     * Tokenize text menjadi words
     *
     * @param string $text
     * @return array
     */
    private function tokenize(string $text): array
    {
        // Split by whitespace
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        return $words ?? [];
    }

    /**
     * Filter stop words dan short words
     *
     * @param array $words
     * @return array
     */
    private function filterWords(array $words): array
    {
        return array_filter($words, function ($word) {
            // Skip stop words
            if (in_array($word, self::STOP_WORDS)) {
                return false;
            }

            // Skip words dengan panjang < 3 characters (except single letter yang penting)
            if (mb_strlen($word, 'UTF-8') < 3) {
                return false;
            }

            // Skip numbers only
            if (is_numeric($word)) {
                return false;
            }

            return true;
        });
    }

    /**
     * Calculate frequency dari setiap word
     *
     * @param array $words
     * @return array
     */
    private function calculateFrequency(array $words): array
    {
        $frequency = array_count_values($words);

        return $frequency ?? [];
    }
}
