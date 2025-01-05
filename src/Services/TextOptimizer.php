<?php

namespace App\Services;

use App\Constant\ModeleKetouva;
use setasign\Fpdi\Tcpdf\Fpdi;

class TextOptimizer
{
    private $pdf;
    private $maxWidth;
    private $x;
    private $y;
    private $lineHeight;
    private $minWidthRatio;
    private $maxWidthRatio;

    public function __construct(Fpdi $pdf, $maxWidth, $x, $y, $lineHeight, $minWidthRatio, $maxWidthRatio)
    {
        $this->pdf = $pdf;
        $this->maxWidth = $maxWidth;
        $this->x = $x;
        $this->y = $y;
        $this->lineHeight = $lineHeight;
        $this->minWidthRatio = $minWidthRatio;
        $this->maxWidthRatio = $maxWidthRatio;
    }

    public function renderText($text)
    {
        $text = preg_replace('/( )+/', ' ', $text); // rempalce les espaces insécables multiple par un seul
        $text = preg_replace('/\s+/', ' ', $text); // remplace les espaces multiples par un seul
        $text = preg_replace('/\s*\(\s*/', '( ', $text); // remplace les espaces avant les parenthèses par un seul
        $text = preg_replace('/  /', ' ', $text); // remplace un espace insécable suivi d'un espace par un seul
        $text = preg_replace('/  /', ' ', $text); // remplace un espace suivi d'un espace insécable par un seul

        $words = preg_split('/\s+/', $text);
        $originalWordCount = count($words);

        $lines = [];
        $currentLine = [];
        $currentWidth = 0;

        // Construction initiale des lignes
        foreach ($words as $word) {
            $wordWidth = $this->pdf->GetStringWidth($word);

            if ($currentWidth + $wordWidth <= $this->maxWidth * $this->maxWidthRatio) {
                $currentLine[] = $word;
                $currentWidth += $wordWidth + $this->pdf->GetStringWidth(' ');
            } else {
                $lines[] = $currentLine;
                $currentLine = [$word];
                $currentWidth = $wordWidth + $this->pdf->GetStringWidth(' ');
            }
        }

        if (!empty($currentLine)) {
            $lines[] = $currentLine;
        }

        // Optimisation des lignes
        $lines = $this->optimizeLines($lines);

        // Vérification et redistribution pour la dernière ligne
        $this->redistributeLastLine($lines);

        // Vérification du nombre final de mots
        $finalWordCount = array_reduce($lines, function ($carry, $line) {
            return $carry + count($line);
        }, 0);

        if ($originalWordCount !== $finalWordCount) {
            throw new \Exception("Le nombre de mots a changé ! Initial : $originalWordCount, Final : $finalWordCount");
        }

        // dd($lines);

        // Rendu des lignes dans le PDF
        $currentY = $this->y;
        foreach ($lines as $line) {
            $lineText = implode(' ', $line);

            if (count($line) > 1) {
                $this->pdf->startTransaction();
                $this->pdf->SetXY($this->x, $currentY);
                $this->pdf->Cell($this->maxWidth, $this->lineHeight, $lineText, 0, 1, 'J');
                $this->pdf->commitTransaction();
            } else {
                $this->pdf->SetXY($this->x, $currentY);
                $this->pdf->Cell($this->maxWidth, $this->lineHeight, $lineText, 0, 1, 'L');
            }
            $currentY += $this->lineHeight;
        }

        // Ajout des lignes NEOUM
        $this->pdf->SetXY($this->x, $currentY);
        $this->pdf->Cell($this->maxWidth, $this->lineHeight, ModeleKetouva::NEOUM, 0, 1, 'J', false);
        $currentY += $this->lineHeight;
        $this->pdf->SetXY($this->x, $currentY);
        $this->pdf->Cell($this->maxWidth, $this->lineHeight, ModeleKetouva::NEOUM, 0, 1, 'J', false);
    }

    private function optimizeLines($lines)
    {
        $lineCount = count($lines);
        for ($i = 0; $i < $lineCount - 1; $i++) {
            $currentLine = $lines[$i];
            $nextLine = $lines[$i + 1];
            $currentWidth = $this->calculateLineWidth($currentLine);

            // Calcul avant de déplacer les mots
            $initialWordCount = count($currentLine) + count($nextLine);

            while (
                $currentWidth < $this->maxWidth * $this->minWidthRatio &&
                !empty($nextLine) &&
                count($nextLine) > 2
            ) {
                // Déplacer un mot de la ligne suivante à la ligne actuelle
                $wordToMove = array_shift($nextLine);
                $currentLine[] = $wordToMove; // Ajouter ce mot à la ligne courante
                $currentWidth = $this->calculateLineWidth($currentLine);
            }

            // Vérifier si des mots ont été déplacés correctement
            $finalWordCount = count($currentLine) + count($nextLine);
            if ($initialWordCount !== $finalWordCount) {
                throw new \Exception("Le nombre de mots a changé après optimisation. Initial : $initialWordCount, Final : $finalWordCount");
            }

            $lines[$i] = $currentLine;
            $lines[$i + 1] = $nextLine;
        }

        return $lines;
    }

    private function redistributeLastLine(&$lines)
    {
        // Filtrer les lignes vides et recalculer les indices
        $lines = array_values(array_filter($lines, function ($line) {
            return !empty($line);
        }));

        $lastLineIndex = count($lines) - 1;
        if ($lastLineIndex < 1) return;

        // Compter les mots totaux avant les déplacements
        $initialWordCount = array_reduce($lines, function ($carry, $line) {
            return $carry + count($line);
        }, 0);

        // Redistribution
        for ($currentLineIndex = $lastLineIndex; $currentLineIndex >= 1; $currentLineIndex--) {
            $currentLine = $lines[$currentLineIndex];
            $currentWidth = $this->calculateLineWidth($currentLine);

            // Vérifier si la ligne est trop courte
            if ($currentWidth < $this->maxWidth * $this->minWidthRatio) {
                $tempLine = $currentLine;
                $tempWidth = $currentWidth;

                while ($tempWidth < $this->maxWidth * $this->minWidthRatio && $currentLineIndex > 0) {
                    $previousLine = $lines[$currentLineIndex - 1];

                    // Si la ligne précédente est trop courte ou n'a plus de mots, arrêter
                    if (count($previousLine) <= 2) break;

                    // Obtenir le dernier mot de la ligne précédente
                    $lastWord = array_pop($previousLine);
                    $wordWidth = $this->pdf->GetStringWidth($lastWord) + $this->pdf->GetStringWidth(' ');

                    // Vérifier si ajouter ce mot dépasserait la largeur
                    if ($tempWidth + $wordWidth > $this->maxWidth) {
                        array_push($previousLine, $lastWord); // Rétablir l'état précédent
                        break;
                    }

                    // Ajouter le mot à la ligne actuelle
                    array_unshift($tempLine, $lastWord);
                    $tempWidth += $wordWidth;

                    // Mettre à jour la ligne précédente
                    $lines[$currentLineIndex - 1] = $previousLine;
                }

                // Mettre à jour la ligne actuelle
                $lines[$currentLineIndex] = $tempLine;
            }
        }

        // Compter les mots totaux après les déplacements
        $finalWordCount = array_reduce($lines, function ($carry, $line) {
            return $carry + count($line);
        }, 0);

        // Vérification stricte du nombre de mots
        if ($initialWordCount !== $finalWordCount) {
            throw new \Exception("Le nombre de mots a changé ! Initial : $initialWordCount, Final : $finalWordCount");
        }
    }

    private function calculateLineWidth($line)
    {
        if (empty($line)) return 0;

        $width = 0;
        foreach ($line as $word) {
            $width += $this->pdf->GetStringWidth($word) + $this->pdf->GetStringWidth(' ');
        }
        return $width - $this->pdf->GetStringWidth(' ');
    }
}
