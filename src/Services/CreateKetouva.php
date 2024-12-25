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
        $modele = str_replace('<br>', '', $modele);
        $modele = str_replace(ModeleKetouva::NEOUM, '', $modele);

        return $modele;
    }


    public function genereTextKetouvaHtml(Ketouva $ketouva)
    {
        $type = $ketouva->getTypeKetouva();

        $provenanceKala = $this->calculeProvenanceKala->getProvenanceKala($ketouva);
        $moisKetouva = $this->calculeMois->getMois($ketouva->getMois(), $ketouva->getJourMois());

        $moisMariage = "";
        $phrasePasDeDate = "";
        $dateMariage = "";

        if ($type == TypeKetouva::TAOUTA) {
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
            case TypeKetouva::HABAD:
                $modele = ModeleKetouva::HABAD;
                break;
            case TypeKetouva::SEFARAD:
                $modele = ModeleKetouva::SEFARAD;
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

            default:
                $modele = '';
                break;
        }

        if ($type == TypeKetouva::TAOUTA || $type == TypeKetouva::IRKESSA) {
            $statutKala = StatutKala::DEFAULT;
            foreach ((new \ReflectionClass(StatutKala::class))->getConstants() as $statut) {
                if ($ketouva->getStatutKala() == $statut['hebreu']) {
                    $statutKala = $statut;
                }
            }

            $modele = str_replace('villeMariage', '<strong><u>' . $ketouva->getVilleMariage() . '</u></strong>', $modele);

            $modele = str_replace('typePaiement', '<strong><u>' . $statutKala['typePaiement'] . '</u></strong>', $modele);
            $modele = str_replace('statutKetouva', '<strong><u>' . $statutKala['statutKetouva'] . '</u></strong>', $modele);
            $modele = str_replace('prix2', '<strong><u>' . $statutKala['prix2'] . '</u></strong>', $modele);
            $modele = str_replace('prix', '<strong><u>' . $statutKala['prix'] . '</u></strong>', $modele);
            $modele = str_replace('bemoitiePrix', '<strong><u>' . $statutKala['beMoitiePrix'] . '</u></strong>', $modele);
            $modele = str_replace('moitiePrix', '<strong><u>' . $statutKala['moitiePrix'] . '</u></strong>', $modele);


            if ($type == TypeKetouva::TAOUTA) {
                $modele = str_replace('jourSemaineMariage', '<strong><u>' . $ketouva->getJourSemaineMariage()->getHebreu() . '</u></strong>', $modele);
                $modele = str_replace('jourMoisMariage', '<strong><u>' . $ketouva->getJourMoisMariage()->getHebreu() . '</u></strong>', $modele);
                $modele = str_replace('moisMariage', '<strong><u>' . $moisMariage . '</u></strong>', $modele);
                $modele = str_replace('anneeMariage', '<strong><u>' . $ketouva->getAnneeMariage()->getHebreu() . '</u></strong>', $modele);
            }

            if ($type == TypeKetouva::IRKESSA) {
                $modele = str_replace('dateMariage', '<strong><u>' . $dateMariage . '</u></strong>', $modele);
                $modele = str_replace('phrasePasDeDate', '<strong><u>' . $phrasePasDeDate . '</u></strong>', $modele);
            }
        }

        $modele = str_replace('jourSemaine', '<strong><u>' . $ketouva->getJourSemaine()->getHebreu() . '</u></strong>', $modele);
        $modele = str_replace('jourMois', '<strong><u>' . $ketouva->getJourMois()->getHebreu() . '</u></strong>', $modele);
        $modele = str_replace('mois', '<strong><u>' . $moisKetouva . '</u></strong>', $modele);
        $modele = str_replace('annee', '<strong><u>' . $ketouva->getAnnee()->getHebreu() . '</u></strong>', $modele);
        $modele = str_replace('ville', '<strong><u>' . $ketouva->getVille() . '</u></strong>', $modele);
        $modele = str_replace('nomHatan', '<strong><u>' . $ketouva->getTitreHatan() . ' ' . $ketouva->getNomHatan() . '</u></strong>', $modele);
        $modele = str_replace('nomPereHatan', '<strong><u>' . $ketouva->getTitrePereHatan() . ' ' . $ketouva->getNomPereHatan() . '</u></strong>', $modele);
        $modele = str_replace('nomKala', '<strong><u>' . $ketouva->getNomKala() . '</u></strong>', $modele);
        $modele = str_replace('nomPereKala', '<strong><u>' . $ketouva->getTitrePereKala() . ' ' . $ketouva->getNomPereKala() . '</u></strong>', $modele);
        $modele = str_replace('provenanceKala', '<strong><u>' . $provenanceKala . '</u></strong>', $modele);

        $modele = str_replace('מדאוריתא', '<strong><u>מדאוריתא</u></strong>', $modele);

        if ($type == TypeKetouva::CINQUANTE || TypeKetouva::TAOUTA || $type == TypeKetouva::IRKESSA) {
            $modele = str_replace('statutKala', '<strong><u>' . $ketouva->getStatutKala() . '</u></strong>', $modele);
        }

        $modele .= '<br>' . ModeleKetouva::NEOUM . '<br>' . ModeleKetouva::NEOUM;

        return $modele;
    }
}
