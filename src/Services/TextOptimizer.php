<?php

namespace App\Services;

class TextOptimizer
{
    private $pdf;
    private $maxWidth;

    public function __construct($pdf, $maxWidth)
    {
        $this->pdf = $pdf;
        $this->maxWidth = $maxWidth;
    }

    public function optimizeText($text)
    {
        // Découper le texte en mots
        $words = preg_split('/\s+/', $text);
        $lines = [];
        $currentLine = [];
        $currentWidth = 0;

        // Réorganiser les mots pour éviter les mauvaises coupures
        foreach ($words as $i => $word) {
            $wordWidth = $this->pdf->GetStringWidth($word);

            // Si c'est un mot très long
            if ($wordWidth > $this->maxWidth * 0.4) {
                // Terminer la ligne courante si elle n'est pas vide
                if (!empty($currentLine)) {
                    // Ajouter des petits mots de la ligne suivante si possible
                    while (isset($words[$i + 1])) {
                        $nextWord = $words[$i + 1];
                        $nextWordWidth = $this->pdf->GetStringWidth($nextWord);
                        if ($currentWidth + $nextWordWidth <= $this->maxWidth * 0.85) {
                            $currentLine[] = $nextWord;
                            $currentWidth += $nextWordWidth + $this->pdf->GetStringWidth(' ');
                            $i++;
                        } else {
                            break;
                        }
                    }
                    $lines[] = implode(' ', $currentLine);
                }

                // Mettre le mot long sur sa propre ligne
                $lines[] = $word;
                $currentLine = [];
                $currentWidth = 0;
                continue;
            }

            // Pour les mots normaux
            if ($currentWidth + $wordWidth <= $this->maxWidth * 0.95) {
                $currentLine[] = $word;
                $currentWidth += $wordWidth + $this->pdf->GetStringWidth(' ');
            } else {
                if (!empty($currentLine)) {
                    $lines[] = implode(' ', $currentLine);
                }
                $currentLine = [$word];
                $currentWidth = $wordWidth + $this->pdf->GetStringWidth(' ');
            }
        }

        // Ajouter la dernière ligne si nécessaire
        if (!empty($currentLine)) {
            $lines[] = implode(' ', $currentLine);
        }

        // Réassembler le texte avec des retours à la ligne doux
        return implode("\n", $lines);
    }
}
