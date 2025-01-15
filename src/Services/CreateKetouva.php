<?php

namespace App\Services;

use App\Constant\ModeleKetouva;
use App\Constant\StatutKala;
use App\Constant\TypeKetouva;
use App\Entity\Ketouva;

class CreateKetouva
{
    private $calculeMois;
    private $calculeProvenanceKala;

    public function __construct(CalculeMois $calculeMois, CalculeProvenanceKala $calculeProvenanceKala)
    {
        $this->calculeMois = $calculeMois;
        $this->calculeProvenanceKala = $calculeProvenanceKala;
    }

    public function genereTextKetouva(Ketouva $ketouva)
    {
        $modele = $this->genereTextKetouvaHtml($ketouva);

        $modele = str_replace('<strong><u>', '', $modele);
        $modele = str_replace('</u></strong>', '', $modele);
        // $modele = str_replace('<strong>', '', $modele);
        // $modele = str_replace('</strong>', '', $modele);
        // $modele = str_replace('<span>', '', $modele);
        // $modele = str_replace('</span>', '', $modele);
        // $modele = str_replace('<br>', '', $modele);
        // $modele = str_replace('&nbsp;', ' ', $modele);
        // $modele = str_replace(ModeleKetouva::NEOUM, '', $modele);

        return $modele;
    }


    public function genereTextKetouvaHtml(Ketouva $ketouva)
    {
        $type = $ketouva->getTypeKetouva();

        // $provenanceKala = $this->calculeProvenanceKala->getProvenanceKala($ketouva);
        $provenanceKala = $ketouva->getProvenanceKala();
        $moisKetouva = $this->calculeMois->getMois($ketouva->getMois(), $ketouva->getJourMois());

        $moisMariage = "";
        $phrasePasDeDate = "";
        $dateMariage = "";

        if ($type == TypeKetouva::TAOUTA || $type == TypeKetouva::NIKREA) {
            $moisMariage = $this->calculeMois->getMois($ketouva->getMoisMariage(), $ketouva->getJourMoisMariage());
        }

        if ($ketouva->getTypeKetouva() == TypeKetouva::IRKESSA) {
            // si on connait pas la date du mariage
            if (!$ketouva->isDateMariageConnue()) {
                $ketouva->setJourSemaineMariage(null);
                $ketouva->setJourMoisMariage(null);
                $ketouva->setMoisMariage(null);
                $ketouva->setAnneeMariage(null);

                $dateMariage = "לי";
                $phrasePasDeDate = "השתא כי סהדי קמאי דחתימו תחות כתובתא קמייתא דאירכסא ליתנייהו וזמן כתובתא קמייתא לא ידענא";
            } else {
                // si on connait la date du mariage
                $moisMariage = $this->calculeMois->getMois($ketouva->getMoisMariage(), $ketouva->getJourMoisMariage());
                $dateMariage = 'לי שהיה ב' . $ketouva->getJourSemaineMariage()->getHebreu() .
                    ' בשבת ' . $ketouva->getJourMoisMariage()->getHebreu() .
                    ' לחדש ' . $moisMariage . ' שנת ' . $ketouva->getAnneeMariage()->getHebreu() . ' ' .
                    'לבריאת העולם';

                $phrasePasDeDate = "השתא";
            }
        }

        switch ($type) {
            case TypeKetouva::BETOULA:
                $modele = ModeleKetouva::BETOULA;
                break;
            case TypeKetouva::CINQUANTE:
                $modele = ModeleKetouva::CINQUANTE;
                break;
            case TypeKetouva::TAOUTA:
                $modele = ModeleKetouva::TAOUTA;
                break;
            case TypeKetouva::IRKESSA:
                $modele = ModeleKetouva::IRKESSA;
                break;
            case TypeKetouva::NIKREA:
                $modele = ModeleKetouva::NIKREA;
                break;

            default:
                $modele = '';
                break;
        }

        if ($type == TypeKetouva::TAOUTA || $type == TypeKetouva::IRKESSA || $type == TypeKetouva::NIKREA) {
            $statutKala = StatutKala::DEFAULT;
            foreach ((new \ReflectionClass(StatutKala::class))->getConstants() as $statut) {
                if ($ketouva->getStatutKala() == $statut['hebreu']) {
                    $statutKala = $statut;
                }
            }

            $modele = str_replace('villeMariage', '<strong><u>' . $ketouva->getVilleMariage() . '</u></strong>', $modele);

            $modele = str_replace('typePaiement', '<strong><u>' . $statutKala['typePaiement'] . '</u></strong>', $modele);
            $modele = str_replace('statutKetouva', '<strong><u>' . $ketouva->getStatutKetouva() . '</u></strong>', $modele);
            $modele = str_replace('prix2', '<strong><u>' . $statutKala['prix2'] . '</u></strong>', $modele);
            $modele = str_replace('prix', '<strong><u>' . $statutKala['prix'] . '</u></strong>', $modele);
            $modele = str_replace('beMoitiePrix', '<strong><u>' . $statutKala['beMoitiePrix'] . '</u></strong>', $modele);
            $modele = str_replace('moitiePrix', '<strong><u>' . $statutKala['moitiePrix'] . '</u></strong>', $modele);


            if ($type == TypeKetouva::TAOUTA || $type == TypeKetouva::NIKREA) {
                $modele = str_replace('jourSemaineMariage', '<strong><u>' . $ketouva->getJourSemaineMariage()->getHebreu() . '</u></strong>', $modele);
                $modele = str_replace('jourMoisMariage', '<strong><u>' . $ketouva->getJourMoisMariage()->getHebreu() . '</u></strong>', $modele);
                $modele = str_replace('moisMariage', '<strong><u>' . $moisMariage . '</u></strong>', $modele);
                $modele = str_replace('anneeMariage', '<strong><u>' . $ketouva->getAnneeMariage()->getHebreu() . '</u></strong>', $modele);
            }

            if ($type == TypeKetouva::IRKESSA) {
                $modele = str_replace('dateMariage', '<strong><u>' . $dateMariage . '</u></strong>', $modele);
                $modele = str_replace('phrasePasDeDate', '<strong><u>' . $phrasePasDeDate . '</u></strong>', $modele);
            }

            if ($type == TypeKetouva::NIKREA) {
                $modele = str_replace('dechirer', '<strong><u>' . $ketouva->getDechirer() . '</u></strong>', $modele);
            }
        }

        $modele = str_replace('jourSemaine', '<strong><u>' . $ketouva->getJourSemaine()->getHebreu() . '</u></strong>', $modele);
        $modele = str_replace('jourMois', '<strong><u>' . $ketouva->getJourMois()->getHebreu() . '</u></strong>', $modele);
        $modele = str_replace('mois', '<strong><u>' . $moisKetouva . '</u></strong>', $modele);
        $modele = str_replace('annee', '<strong><u>' . $ketouva->getAnnee()->getHebreu() . '</u></strong>', $modele);
        $modele = str_replace('ville', '<strong><u>' . $ketouva->getVille() . '</u></strong>', $modele);
        $nomHatan = $ketouva->getTitreHatan() . ' ' . $ketouva->getNomHatan();
        $nomHatan = str_replace(' ',  ' ', $nomHatan);
        $modele = str_replace('nomHatan', '<strong><u>' . $nomHatan . '</u></strong>', $modele);
        $nomFamilleHatan = str_replace(' ',  ' ', $ketouva->getNomFamilleHatan());
        $modele = str_replace('nomFamilleHatan', '<strong><u>' . $nomFamilleHatan . '</u></strong>', $modele);
        $nomPereHatan = str_replace(' ',  ' ', $ketouva->getNomPereHatan());
        $modele = str_replace('nomPereHatan', '<strong><u>' . $ketouva->getTitrePereHatan() . ' ' . $nomPereHatan . '</u></strong>', $modele);
        $nomKala = str_replace(' ',  ' ', $ketouva->getNomKala());
        $modele = str_replace('nomKala', '<strong><u>' . $nomKala . '</u></strong>', $modele);
        $nomFamilleKala = str_replace(' ',  ' ', $ketouva->getNomFamilleKala());
        $modele = str_replace('nomFamilleKala', '<strong><u>' . $nomFamilleKala . '</u></strong>', $modele);
        $nomPereKala = str_replace(' ',  ' ', $ketouva->getNomPereKala());
        $modele = str_replace('nomPereKala', '<strong><u>' . $ketouva->getTitrePereKala() . ' ' . $nomPereKala . '</u></strong>', $modele);
        $modele = str_replace('provenanceKala', '<strong><u>' . $provenanceKala . '</u></strong>', $modele);
        $modele = str_replace('statutKetouva', '<strong><u>' . $ketouva->getStatutKetouva() . '</u></strong>', $modele);

        if ($type == TypeKetouva::CINQUANTE || TypeKetouva::TAOUTA || $type == TypeKetouva::IRKESSA || $type == TypeKetouva::NIKREA) {
            $modele = str_replace('statutKala', '<strong><u>' . $ketouva->getStatutKala() . '</u></strong>', $modele);
        }

        // séparer le modele en mots
        // $mots = explode(" ", $modele);

        // met en gras les 3 derniers mots du modele
        // if (count($mots) >= 3) {
        //     $mots[count($mots) - 3] = '<span style="font-size:130%">' . $mots[count($mots) - 3];
        //     $mots[count($mots) - 1] = $mots[count($mots) - 1] . '</span>';
        //     $modele = implode(" ", $mots);
        // }

        // mettre un span autour des 8 derniers mots
        // if (count($mots) >= 8) {
        //     $mots[count($mots) - 8] = '<span>' . $mots[count($mots) - 8];
        //     $mots[count($mots) - 1] = $mots[count($mots) - 1] . '</span>';
        //     $modele = implode(" ", $mots);
        // }


        // $modele .= '<br>' . ModeleKetouva::NEOUM . '<br>' . ModeleKetouva::NEOUM;

        return $modele;
    }

    public static function getTexteVersoPDF(Ketouva $ketouva)
    {
        $texteVerso = "Ce Mariage a été célebré par le R. Mordehai EDERHY <br>";
        if ($ketouva->getTypeKetouva() == TypeKetouva::TAOUTA || $ketouva->getTypeKetouva() == TypeKetouva::IRKESSA || $ketouva->getTypeKetouva() == TypeKetouva::NIKREA) {
            $texteVerso = "Cette Ketouba a été rédigée par le R. Mordehai EDERHY <br> ";
        }

        $texteVerso .=  "Le " . $ketouva->getDateFrancais() . ", à " . $ketouva->getCodePostalFrancais() . " "  . $ketouva->getVilleFrancais() . " <br>";
        $texteVerso .=  "Email : mordehai.edri@gmail.com - Téléphone : 07 69 68 81 15";
        return $texteVerso;
    }

    public static function getTexteVersoWord(Ketouva $ketouva)
    {
        $texteVerso = self::getTexteVersoPDF($ketouva);
        $texteVerso = str_replace('<br>', "\n", $texteVerso);
        return $texteVerso;
    }
}
