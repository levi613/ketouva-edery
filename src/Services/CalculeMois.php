<?php

namespace App\Services;

use App\Constant\TypeKetouva;
use App\Entity\JourMois;
use App\Entity\Ketouva;
use App\Entity\Mois;
use App\Repository\MoisRepository;

class CalculeMois
{
    protected $moisRepository;

    public function __construct(MoisRepository $moisRepository)
    {
        $this->moisRepository = $moisRepository;
    }

    public function getMois(Mois $mois, JourMois $jourMois)
    {
        // si c'est le 30 du mois, il faut dire que cest roch hodech du mois suivant
        $moisHebreu = $mois->getHebreu();
        if ($jourMois->getNum() == 30) {
            $numMoisSuivant = $mois->getNum() + 1;
            switch ($mois->getNum()) {
                    // chevat devient adar
                case 5:
                    $numMoisSuivant = 14;
                    break;

                    // adar et adar bet deviennent nissan
                case 14:
                case 7:
                    $numMoisSuivant = 8;
                    break;

                    // adar alef devient adar bet
                case 6:
                    $numMoisSuivant = 7;
                    break;

                    // eloul devient tichri
                case 13:
                    $numMoisSuivant = 1;
                    break;

                    // si non le mois suivant reste le mois renseigné + 1
                default:
                    break;
            }
            $moisSuivant = $this->moisRepository->findOneBy(['num' => $numMoisSuivant]);
            $moisHebreu = $mois->getHebreu() . ' ' . Mois::cheou_roch_hodech . ' ' . $moisSuivant->getHebreu();
        }

        return $moisHebreu;
    }
}
