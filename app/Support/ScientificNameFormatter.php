<?php

namespace App\Support;

class ScientificNameFormatter
{
    public static function formatName(string $text): string
    {
        return self::looksLikeScientificName($text)
            ? '<em>'.e($text).'</em>'
            : e($text);
    }

    /**
     * @param  array<int, string>  $candidateScientificNames
     */
    public static function formatText(string $text, array $candidateScientificNames = []): string
    {
        $scientificNames = collect($candidateScientificNames)
            ->filter(fn ($name) => is_string($name) && self::looksLikeScientificName($name))
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->unique()
            ->sortByDesc(fn (string $name) => strlen($name))
            ->values()
            ->all();

        if ($scientificNames === []) {
            return e($text);
        }

        $preparedText = $text;
        $replacements = [];

        foreach ($scientificNames as $index => $name) {
            $token = "__SCIENTIFIC_NAME_{$index}__";
            $pattern = '/(?<!\p{L})'.preg_quote($name, '/').'(?!\p{L})/u';

            $preparedText = preg_replace($pattern, $token, $preparedText) ?? $preparedText;
            $replacements[$token] = '<em>'.e($name).'</em>';
        }

        return strtr(e($preparedText), $replacements);
    }

    public static function looksLikeScientificName(string $text): bool
    {
        $normalized = trim(preg_replace('/\s+/', ' ', $text) ?? $text);

        return preg_match(
            '/^[A-Z][a-z]+(?:-[A-Z][a-z]+)?\s(?:[a-z]+(?:-[a-z]+)?|sp\.\s?\d+)(?:\s[a-z]+(?:-[a-z]+)?)?$/',
            $normalized
        ) === 1;
    }
}
