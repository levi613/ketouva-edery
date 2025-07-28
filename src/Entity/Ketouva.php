<?php

namespace App\Entity;

use App\Constant\TitrePersonne;
use App\Repository\KetouvaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KetouvaRepository::class)]
class Ketouva
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $typeKetouva = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?JourSemaine $jourSemaine = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?JourMois $JourMois = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mois $mois = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Annee $annee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titreHatan = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomHatan = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titrePereHatan = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomPereHatan = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomKala = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titrePereKala = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomPereKala = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statutKala = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $provenanceKala = null;

    #[ORM\Column(nullable: true)]
    private ?bool $orpheline = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $villeMariage = null;

    #[ORM\ManyToOne]
    private ?JourSemaine $jourSemaineMariage = null;

    #[ORM\ManyToOne]
    private ?JourMois $jourMoisMariage = null;

    #[ORM\ManyToOne]
    private ?Mois $moisMariage = null;

    #[ORM\ManyToOne]
    private ?Annee $anneeMariage = null;

    #[ORM\Column(nullable: true)]
    private ?bool $dateMariageConnue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomFichier = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $editedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statutKetouva = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dechirer = null;

    #[ORM\Column(nullable: true)]
    private ?float $ajustFontSizeInPdf = null;

    #[ORM\Column(nullable: true)]
    private ?float $ecartLigne = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $salleFrancais = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $villeFrancais = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codePostalFrancais = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dateFrancais = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomFamilleHatan = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomFamilleKala = null;

    #[ORM\Column(nullable: true)]
    private ?bool $hatanBahour = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $optionPissoul1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $optionPissoul2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prixPissoul = null;

    public function __construct()
    {
        // titre personne par default  (reb)
        $titreDefault = TitrePersonne::REB;

        $this->titreHatan = $titreDefault;
        $this->titrePereHatan = $titreDefault;
        $this->titrePereKala = $titreDefault;

        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeKetouva(): ?string
    {
        return $this->typeKetouva;
    }

    public function setTypeKetouva(string $typeKetouva): static
    {
        $this->typeKetouva = $typeKetouva;

        return $this;
    }

    public function getJourSemaine(): ?JourSemaine
    {
        return $this->jourSemaine;
    }

    public function setJourSemaine(?JourSemaine $jourSemaine): static
    {
        $this->jourSemaine = $jourSemaine;

        return $this;
    }

    public function getJourMois(): ?JourMois
    {
        return $this->JourMois;
    }

    public function setJourMois(?JourMois $JourMois): static
    {
        $this->JourMois = $JourMois;

        return $this;
    }

    public function getMois(): ?Mois
    {
        return $this->mois;
    }

    public function setMois(?Mois $mois): static
    {
        $this->mois = $mois;

        return $this;
    }

    public function getAnnee(): ?Annee
    {
        return $this->annee;
    }

    public function setAnnee(?Annee $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getTitreHatan(): ?string
    {
        return $this->titreHatan;
    }

    public function setTitreHatan(?string $titreHatan): static
    {
        $this->titreHatan = $titreHatan;

        return $this;
    }

    public function getNomHatan(): ?string
    {
        return $this->nomHatan;
    }

    public function setNomHatan(?string $nomHatan): static
    {
        $this->nomHatan = $nomHatan;

        return $this;
    }

    public function getTitrePereHatan(): ?string
    {
        return $this->titrePereHatan;
    }

    public function setTitrePereHatan(?string $titrePereHatan): static
    {
        $this->titrePereHatan = $titrePereHatan;

        return $this;
    }

    public function getNomPereHatan(): ?string
    {
        return $this->nomPereHatan;
    }

    public function setNomPereHatan(?string $nomPereHatan): static
    {
        $this->nomPereHatan = $nomPereHatan;

        return $this;
    }

    public function getNomKala(): ?string
    {
        return $this->nomKala;
    }

    public function setNomKala(?string $nomKala): static
    {
        $this->nomKala = $nomKala;

        return $this;
    }

    public function getTitrePereKala(): ?string
    {
        return $this->titrePereKala;
    }

    public function setTitrePereKala(?string $titrePereKala): static
    {
        $this->titrePereKala = $titrePereKala;

        return $this;
    }

    public function getNomPereKala(): ?string
    {
        return $this->nomPereKala;
    }

    public function setNomPereKala(?string $nomPereKala): static
    {
        $this->nomPereKala = $nomPereKala;

        return $this;
    }

    public function getStatutKala(): ?string
    {
        return $this->statutKala;
    }

    public function setStatutKala(?string $statutKala): static
    {
        $this->statutKala = $statutKala;

        return $this;
    }

    public function getProvenanceKala(): ?string
    {
        return $this->provenanceKala;
    }

    public function setProvenanceKala(?string $provenanceKala): static
    {
        $this->provenanceKala = $provenanceKala;

        return $this;
    }

    public function isOrpheline(): ?bool
    {
        return $this->orpheline;
    }

    public function setOrpheline(?bool $orpheline): static
    {
        $this->orpheline = $orpheline;

        return $this;
    }

    public function getVilleMariage(): ?string
    {
        return $this->villeMariage;
    }

    public function setVilleMariage(?string $villeMariage): static
    {
        $this->villeMariage = $villeMariage;

        return $this;
    }

    public function getJourSemaineMariage(): ?JourSemaine
    {
        return $this->jourSemaineMariage;
    }

    public function setJourSemaineMariage(?JourSemaine $jourSemaineMariage): static
    {
        $this->jourSemaineMariage = $jourSemaineMariage;

        return $this;
    }

    public function getJourMoisMariage(): ?JourMois
    {
        return $this->jourMoisMariage;
    }

    public function setJourMoisMariage(?JourMois $jourMoisMariage): static
    {
        $this->jourMoisMariage = $jourMoisMariage;

        return $this;
    }

    public function getMoisMariage(): ?Mois
    {
        return $this->moisMariage;
    }

    public function setMoisMariage(?Mois $moisMariage): static
    {
        $this->moisMariage = $moisMariage;

        return $this;
    }

    public function getAnneeMariage(): ?Annee
    {
        return $this->anneeMariage;
    }

    public function setAnneeMariage(?Annee $anneeMariage): static
    {
        $this->anneeMariage = $anneeMariage;

        return $this;
    }

    public function isDateMariageConnue(): ?bool
    {
        return $this->dateMariageConnue;
    }

    public function setDateMariageConnue(?bool $dateMariageConnue): static
    {
        $this->dateMariageConnue = $dateMariageConnue;

        return $this;
    }

    public function getNomFichier(): ?string
    {
        return $this->nomFichier;
    }

    public function setNomFichier(?string $nomFichier): static
    {
        $this->nomFichier = $nomFichier;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEditedAt(): ?\DateTimeInterface
    {
        return $this->editedAt;
    }

    public function setEditedAt(?\DateTimeInterface $editedAt): static
    {
        $this->editedAt = $editedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getStatutKetouva(): ?string
    {
        return $this->statutKetouva;
    }

    public function setStatutKetouva(?string $statutKetouva): static
    {
        $this->statutKetouva = $statutKetouva;

        return $this;
    }

    public function getDechirer(): ?string
    {
        return $this->dechirer;
    }

    public function setDechirer(?string $dechirer): static
    {
        $this->dechirer = $dechirer;

        return $this;
    }

    public function getAjustFontSizeInPdf(): ?float
    {
        return $this->ajustFontSizeInPdf;
    }

    public function setAjustFontSizeInPdf(?float $ajustFontSizeInPdf): static
    {
        $this->ajustFontSizeInPdf = $ajustFontSizeInPdf;

        return $this;
    }

    public function getEcartLigne(): ?float
    {
        return $this->ecartLigne;
    }

    public function setEcartLigne(?float $ecartLigne): static
    {
        $this->ecartLigne = $ecartLigne;

        return $this;
    }

    public function getSalleFrancais(): ?string
    {
        return $this->salleFrancais;
    }

    public function setSalleFrancais(?string $salleFrancais): static
    {
        $this->salleFrancais = $salleFrancais;

        return $this;
    }

    public function getVilleFrancais(): ?string
    {
        return $this->villeFrancais;
    }

    public function setVilleFrancais(?string $villeFrancais): static
    {
        $this->villeFrancais = $villeFrancais;

        return $this;
    }

    public function getCodePostalFrancais(): ?string
    {
        return $this->codePostalFrancais;
    }

    public function setCodePostalFrancais(?string $codePostalFrancais): static
    {
        $this->codePostalFrancais = $codePostalFrancais;

        return $this;
    }

    public function getDateFrancais(): ?string
    {
        return $this->dateFrancais;
    }

    public function setDateFrancais(?string $dateFrancais): static
    {
        $this->dateFrancais = $dateFrancais;

        return $this;
    }

    public function getNomFamilleHatan(): ?string
    {
        return $this->nomFamilleHatan;
    }

    public function setNomFamilleHatan(?string $nomFamilleHatan): static
    {
        $this->nomFamilleHatan = $nomFamilleHatan;

        return $this;
    }

    public function getNomFamilleKala(): ?string
    {
        return $this->nomFamilleKala;
    }

    public function setNomFamilleKala(?string $nomFamilleKala): static
    {
        $this->nomFamilleKala = $nomFamilleKala;

        return $this;
    }

    public function isHatanBahour(): ?bool
    {
        return $this->hatanBahour;
    }

    public function setHatanBahour(?bool $hatanBahour): static
    {
        $this->hatanBahour = $hatanBahour;

        return $this;
    }

    public function getOptionPissoul1(): ?string
    {
        return $this->optionPissoul1;
    }

    public function setOptionPissoul1(?string $optionPissoul1): static
    {
        $this->optionPissoul1 = $optionPissoul1;

        return $this;
    }

    public function getOptionPissoul2(): ?string
    {
        return $this->optionPissoul2;
    }

    public function setOptionPissoul2(?string $optionPissoul2): static
    {
        $this->optionPissoul2 = $optionPissoul2;

        return $this;
    }

    public function getPrixPissoul(): ?string
    {
        return $this->prixPissoul;
    }

    public function setPrixPissoul(?string $prixPissoul): static
    {
        $this->prixPissoul = $prixPissoul;

        return $this;
    }
}
