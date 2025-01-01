<?php

namespace App\Services;

use App\Constant\ModeleKetouva;
use App\Constant\TypeKetouva;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF_FONTS;

class PdfGeneratorService
{
    private const CM_TO_MM = 10; // Conversion cm to mm

    public function generatePdf(string $text, $modele, $nomFichier, $typeKetouva): string
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

        $pdf->setCellHeightRatio(1.4); // Ajustement de la hauteur des cellules
        $fontSize = 18;
        if ($typeKetouva == TypeKetouva::IRKESSA || $typeKetouva == TypeKetouva::TAOUTA || $typeKetouva == TypeKetouva::NIKREA) {
            $fontSize = 15.3;
            $pdf->setCellHeightRatio(1.3); // Ajustement de la hauteur des cellules
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

        // Positionnement et écriture du texte justifié
        $pdf->SetXY($x, $y);
        $pdf->MultiCell($width, 5, $text, 0, 'J', false, 1, $x, $y, true, 0, true, true, $height, 'T', true);

        // ecrire le texte en bas de la page
        if ($typeKetouva == TypeKetouva::TAOUTA || $typeKetouva == TypeKetouva::IRKESSA || $typeKetouva == TypeKetouva::NIKREA) {
            // Modele 1
            $x = 53; //
            $y = 363;

            // Modele 2
            if (str_contains($modele, '2')) {
                $x = 50; // 4.04cm
                $y = 340; // 9.36cm
                $width = 216.2; // 21.62cm
                $height = 249.7; // 24.97cm
            }

            // Positionnement et écriture du texte justifié
            $pdf->SetXY($x, $y);
            $pdf->MultiCell($width, 5, ModeleKetouva::FIN_REECRITURE, 0, 'J', false, 1, $x, $y, true, 0, true, true, $height, 'T', true);
        }

        // Retourne le PDF sous forme de chaîne
        return $pdf->Output($nomFichier, 'S'); // pour telecharger le PDF
        // return $pdf->Output($nomFichier, 'I'); // Affichage du PDF dans le navigateur
    }
}
