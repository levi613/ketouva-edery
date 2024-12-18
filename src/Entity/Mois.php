<?php

namespace App\Entity;

use App\Repository\MoisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MoisRepository::class)]
class Mois
{
    public const cheou_roch_hodech = "שהוא ראש חדש";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $num = null;

    #[ORM\Column(length: 255)]
    private ?string $francais = null;

    #[ORM\Column(length: 255)]
    private ?string $hebreu = null;

    public function __construct(int $num = null, string $francais = null, string $hebreu = null)
    {
        $this->num = $num;
        $this->francais = $francais;
        $this->hebreu = $hebreu;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNum(): ?int
    {
        return $this->num;
    }

    public function setNum(int $num): static
    {
        $this->num = $num;

        return $this;
    }

    public function getFrancais(): ?string
    {
        return $this->francais;
    }

    public function setFrancais(string $francais): static
    {
        $this->francais = $francais;

        return $this;
    }

    public function getHebreu(): ?string
    {
        return $this->hebreu;
    }

    public function setHebreu(string $hebreu): static
    {
        $this->hebreu = $hebreu;

        return $this;
    }
}
