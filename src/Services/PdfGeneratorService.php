<?php

namespace App\Services;

use App\Constant\ModeleKetouva;
use App\Constant\TypeKetouva;
use App\Entity\Ketouva;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF_FONTS;

class PdfGeneratorService
{

    public function generatePdf(string $text, $modele, Ketouva $ketouva): string
    {
        // Création d'un nouveau PDF au format A3
        $pdf = new Fpdi('P', 'mm', 'A3', true, 'UTF-8', false);

        // Configuration de base
        $pdf->SetCreator('Ketouva');
        $pdf->SetAuthor('Rav Edery');
        $pdf->SetTitle('Ketouva');

        // Suppression des en-têtes et pieds de page par défaut
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Support de l'hébreu (RTL)
        $pdf->setRTL(true);

        // Ajout de la police personnalisée
        $fontPath = __DIR__ . '/../../public/font/ShlomoStam.ttf';
        $fontFamily = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);

        // Ajout d'une nouvelle page
        $pdf->AddPage();

        // Import du template
        $templatePath = __DIR__ . '/../../public/assets/modele/modele' . $modele . '.pdf';
        $pdf->setSourceFile($templatePath);
        $pageId = $pdf->importPage(1);
        $pdf->useTemplate($pageId, 0, 0, null, null, true);

        // Ajustement de l'opacité du texte pour le rendre plus clair
        $pdf->setAlpha(0.8);

        $fontSize = 16.2;
        $lineHeight = $ketouva->getEcartLigne();
        if (!is_numeric($lineHeight) || $lineHeight === null || $lineHeight === "") {
            $lineHeight = 9;
        }
        if ($ketouva->getTypeKetouva() == TypeKetouva::IRKESSA || $ketouva->getTypeKetouva() == TypeKetouva::TAOUTA || $ketouva->getTypeKetouva() == TypeKetouva::NIKREA) {
            $fontSize = 12.9;

            if (!is_numeric($lineHeight) || $lineHeight === null || $lineHeight === "") {
                $lineHeight = 7.7;
            }
        }
        if (str_contains($modele, '3')) {
            $fontSize--;
        }

        try {
            $fontSize += $ketouva->getAjustFontSizeInPdf();
        } catch (\Throwable $th) {
        }

        // Configuration de la police
        $pdf->SetFont($fontFamily, '', $fontSize);

        // Modele 1
        $x = 45.4; // 4.54cm
        $y = 103.5; // 10.35cm
        $width = 206.1; // 20.61cm
        $height = 266.7; // 26.67cm

        // Modele 2
        if (str_contains($modele, '2')) {
            $x = 40.4; // 4.04cm
            $y = 93.6; // 9.36cm
            $width = 216.2; // 21.62cm
            $height = 249.7; // 24.97cm
        }

        // Modele 3
        if (str_contains($modele, '3')) {
            $x = 46.1; // 4.61cm
            $y = 113; // 11.3cm
            $width = 204.9; // 20.49cm
            $height = 243.9; // 24.39cm
        }

        // Après la configuration de la police et avant le MultiCell
        $optimizer = new TextOptimizer($pdf, $width, $x, $y, $lineHeight, 0.8, 0.93);
        $optimizer->renderText($text);

        // $pdf->SetXY($x, $y);
        // $pdf->MultiCell($width, 5, $optimizedText, 0, 'J', false, 1, $x, $y, true, 0, true, true, $height, 'T', true);

        // ecrire le texte en bas de la page
        if ($ketouva->getTypeKetouva() == TypeKetouva::TAOUTA || $ketouva->getTypeKetouva() == TypeKetouva::IRKESSA || $ketouva->getTypeKetouva() == TypeKetouva::NIKREA) {
            // Modele 1
            $x = 53; //
            $y = 363;

            // Modele 2
            if (str_contains($modele, '2')) {
                $x = 50; // 4.04cm
                $y = 340; // 9.36cm
            }

            // Modele 3
            if (str_contains($modele, '3')) {
                $x = 65; // 4.04cm
                $y = 350; // 9.36cm
            }

            // Positionnement et écriture du texte justifié
            $pdf->SetXY($x, $y);
            $pdf->MultiCell($width, 5, ModeleKetouva::FIN_REECRITURE, 0, 'J', false, 1, $x, $y, true, 0, true, true, $height, 'T', true);
        }

        $pdf->AddPage();
        $pdf->setRTL(false);

        // Ajout de la police personnalisée
        $fontPath = __DIR__ . '/../../public/font/PinyonScript-Regular.ttf';
        $fontFamily = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 32);
        // Configuration de la police
        $pdf->SetFont($fontFamily, 'I', 20);

        // Dimensions de la page
        $pageWidth = $pdf->getPageWidth();
        $pageHeight = $pdf->getPageHeight();

        // Position du texte (vers le bas de la page)
        $marginBottom = 60; // Marge par rapport au bas de la page
        $yPosition = $pageHeight - $marginBottom;

        // Largeur de la cellule (centrée sur la page)
        $cellWidth = $pageWidth * 0.8; // Par exemple, 80% de la largeur de la page
        $xPosition = ($pageWidth - $cellWidth) / 2;

        $texteVerso = CreateKetouva::getTexteVersoPDF($ketouva);

        // Ajouter le texte avec MultiCell
        $pdf->SetXY($xPosition, $yPosition);
        $pdf->MultiCell(
            $cellWidth,        // Largeur de la cellule
            10,                // Hauteur de chaque ligne
            $texteVerso,       // Texte à afficher
            0,                 // Pas de bordure
            'C',               // Texte centré
            false,             // Pas de fond
            1,
            null,
            null,
            true,
            0,
            true               // Texte en html
        );




        // Retourne le PDF
        // return $pdf->Output($ketouva->getNomFichier(), 'I'); // Affichage du PDF dans le navigateur
        return $pdf->Output($ketouva->getNomFichier(), 'S'); // pour telecharger le PDF
    }
}
