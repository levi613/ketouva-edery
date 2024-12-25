<?php

namespace App\Services;

use App\Constant\ProvenanceKala;
use App\Constant\StatutKala;
use App\Constant\TypeKetouva;
use App\Entity\Ketouva;
use App\Repository\ProvenanceKalaRepository;

class CalculeProvenanceKala
{
    public function getProvenanceKala(Ketouva $ketouva)
    {
        $provenanceKala = ProvenanceKala::PERE;
        if ($ketouva->isOrpheline()) {
            $provenanceKala = ProvenanceKala::ELLE;
        }

        if ($ketouva->getTypeKetouva() == TypeKetouva::CINQUANTE) {
            $provenanceKala = ProvenanceKala::ELLE;
            if ($ketouva->getStatutKala() == StatutKala::CONVERTIE['hebreu']) {
                $provenanceKala = ProvenanceKala::RIEN;
            }
        }

        if ($ketouva->getTypeKetouva() == TypeKetouva::TAOUTA || $ketouva->getTypeKetouva() == TypeKetouva::IRKESSA) {

            if ($ketouva->getStatutKala() == StatutKala::BETOULA['hebreu']) {
                $provenanceKala = ProvenanceKala::PERE;
            } elseif ($ketouva->getStatutKala() == StatutKala::NON_BETOULA['hebreu']) {
                $provenanceKala = ProvenanceKala::ELLE;
            } else {
                $provenanceKala = ProvenanceKala::RIEN;
            }

            if ($ketouva->getStatutKala() == StatutKala::BETOULA['hebreu'] && $ketouva->isOrpheline()) {
                // mibé nacha
                $provenanceKala = ProvenanceKala::ELLE;
                $ketouva->setProvenanceKala($provenanceKala);
            }
        }

        return $provenanceKala;
    }
}
