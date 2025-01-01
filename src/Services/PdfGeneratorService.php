<?php

namespace App\Services;

use App\Constant\ModeleKetouva;
use App\Constant\TypeKetouva;
use Mpdf\Mpdf;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF_FONTS;

class PdfGeneratorService
{

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
        if (str_contains($modele, '3')) {
            $fontSize--;
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
            $y = 118.3; // 11.83cm
            $width = 204.9; // 20.49cm
            $height = 243.9; // 24.39cm
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

        // Retourne le PDF sous forme de chaîne
        // return $pdf->Output($nomFichier, 'I'); // Affichage du PDF dans le navigateur
        return $pdf->Output($nomFichier, 'S'); // pour telecharger le PDF
    }
}












//         $mpdf = new Mpdf([
//             'format' => 'A3',
//             'orientation' => 'P',
//             'margin_left' => 45.4,
//             'margin_top' => 103.5,
//             'default_font' => 'dejavusans'
//         ]);

//         // CSS pour forcer la justification complète
//         $css = "
//             .justified {
//                 text-align: justify;
//                 text-align-last: justify;
//             }
//         ";
//         $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);

//         // Votre texte
//         $html = "<div class='justified'>{$text}</div>";
//         $mpdf->WriteHTML($html);

//         return $mpdf->Output($nomFichier, \Mpdf\Output\Destination::FILE);
//     }
// }





// namespace App\Services;

// use App\Constant\ModeleKetouva;
// use App\Constant\TypeKetouva as ConstantTypeKetouva;
// use Mpdf\Mpdf;

// class PdfGeneratorService
// {
//     private Mpdf $mpdf;

//     public function __construct()
//     {
//         $this->initializeMpdf();
//     }

//     private function initializeMpdf(): void
//     {
//         // Définir le répertoire des polices personnalisées
//         $customFontDir = __DIR__ . '/../../public/font';

//         $this->mpdf = new Mpdf([
//             'format' => 'A3',
//             'orientation' => 'P',
//             'margin_left' => 0,
//             'margin_top' => 0,
//             'margin_right' => 0,
//             'margin_bottom' => 0,
//             'fontDir' => [$customFontDir], // Ajout du répertoire des polices
//             'fontdata' => [
//                 'shlomostam' => [
//                     'R' => 'ShlomoStam.ttf',
//                     'useOTL' => 0xFF, // Activation du support OpenType
//                     'useKashida' => 75, // Support pour l'arabe/hébreu
//                 ]
//             ],
//             'default_font' => 'shlomostam'
//         ]);

//         // Configuration de base
//         $this->mpdf->SetCreator('Ketouva');
//         $this->mpdf->SetAuthor('Rav Edery');
//         $this->mpdf->SetTitle('Ketouva');

//         // Support de l'hébreu (RTL)
//         $this->mpdf->SetDirectionality('rtl');
//     }

//     public function generatePdf(string $text, string $modele, string $nomFichier, string $typeKetouva): string
//     {
//         // Ajout de la police personnalisée
//         $fontPath = __DIR__ . '/../../public/font/ShlomoStam.ttf';
//         $this->mpdf->AddFont('ShlomoStam', '', $fontPath);

//         // Import du template
//         $templatePath = __DIR__ . '/../../public/assets/modele/modele' . $modele . '.pdf';
//         $this->mpdf->SetDocTemplate($templatePath, true);

//         // Ajout d'une nouvelle page
//         $this->mpdf->AddPage();

//         // Configuration du style
//         $this->configureStyle($typeKetouva, $modele);

//         // Écriture du contenu principal
//         $this->writeMainContent($text, $modele);

//         // Écriture du texte en bas si nécessaire
//         if ($this->needsFooterText($typeKetouva)) {
//             $this->writeFooterText($modele);
//         }

//         return $this->mpdf->Output($nomFichier, \Mpdf\Output\Destination::INLINE);
//     }

//     private function configureStyle(string $typeKetouva, string $modele): void
//     {
//         $fontSize = $this->getFontSize($typeKetouva);
//         $positions = $this->getPositions($modele);

//         $css = "
//             .ketouva-text {
//                 font-family: ShlomoStam;
//                 font-size: {$fontSize}pt;
//                 text-align: justify;
//                 position: absolute;
//                 top: {$positions['y']}mm;
//                 left: {$positions['x']}mm;
//                 width: {$positions['width']}mm;
//                 height: {$positions['height']}mm;
//                 opacity: 0.8;
//             }
//         ";

//         $this->mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
//     }

//     private function writeMainContent(string $text, string $modele): void
//     {
//         // Ajouter un espace invisible à la fin du texte pour forcer la justification
//         $text = $this->addInvisibleJustification($text);
//         $html = "<div class='ketouva-text'>{$text}</div>";
//         $this->mpdf->WriteHTML($html);
//     }

//     private function writeFooterText(string $modele): void
//     {
//         $positions = $this->getFooterPositions($modele);
//         $css = "
//             .footer-text {
//                 position: absolute;
//                 top: {$positions['y']}mm;
//                 left: {$positions['x']}mm;
//                 text-align: justify;
//                 text-align-last: justify;
//             }
//         ";
//         $this->mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);

//         $html = "<div class='footer-text'>" . ModeleKetouva::FIN_REECRITURE . "</div>";
//         $this->mpdf->WriteHTML($html);
//     }

//     private function getFontSize(string $typeKetouva): float
//     {
//         return match ($typeKetouva) {
//             ConstantTypeKetouva::IRKESSA, ConstantTypeKetouva::TAOUTA, ConstantTypeKetouva::NIKREA => 15.3,
//             default => 18
//         };
//     }

//     private function getPositions(string $modele): array
//     {
//         if (str_contains($modele, '2')) {
//             return [
//                 'x' => 40.4,
//                 'y' => 93.6,
//                 'width' => 216.2,
//                 'height' => 249.7
//             ];
//         }

//         return [
//             'x' => 45.4,
//             'y' => 103.5,
//             'width' => 206.1,
//             'height' => 266.7
//         ];
//     }

//     private function getFooterPositions(string $modele): array
//     {
//         if (str_contains($modele, '2')) {
//             return ['x' => 50, 'y' => 340];
//         }
//         return ['x' => 53, 'y' => 363];
//     }

//     private function needsFooterText(string $typeKetouva): bool
//     {
//         return in_array($typeKetouva, [
//             ConstantTypeKetouva::TAOUTA,
//             ConstantTypeKetouva::IRKESSA,
//             ConstantTypeKetouva::NIKREA
//         ]);
//     }

//     private function addInvisibleJustification(string $text): string
//     {
//         // Ajouter des espaces invisibles pour forcer la justification
//         return $text . '&#8203;&#8203;&#8203;&#8203;&#8203;';
//     }
// }
