<?php

namespace App\Entity;

use App\Repository\PartenairePhysiqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartenairePhysiqueRepository::class)]
class PartenairePhysique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\Column(length: 100)]
    private ?string $nomFamille = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateNaissance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieuNaissance = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nationalite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profession = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $numeroSecu = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $numeroCNI = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $situationFamiliale = null;

    #[ORM\Column(nullable: true)]
    private ?bool $casierJudiciaire = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $fortuneEstimee = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNomFamille(): ?string
    {
        return $this->nomFamille;
    }

    public function setNomFamille(string $nomFamille): static
    {
        $this->nomFamille = $nomFamille;

        return $this;
    }

    public function getDateNaissance(): ?\DateTime
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTime $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getLieuNaissance(): ?string
    {
        return $this->lieuNaissance;
    }

    public function setLieuNaissance(?string $lieuNaissance): static
    {
        $this->lieuNaissance = $lieuNaissance;

        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(?string $nationalite): static
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(?string $profession): static
    {
        $this->profession = $profession;

        return $this;
    }

    public function getNumeroSecu(): ?string
    {
        return $this->numeroSecu;
    }

    public function setNumeroSecu(?string $numeroSecu): static
    {
        $this->numeroSecu = $numeroSecu;

        return $this;
    }

    public function getNumeroCNI(): ?string
    {
        return $this->numeroCNI;
    }

    public function setNumeroCNI(?string $numeroCNI): static
    {
        $this->numeroCNI = $numeroCNI;

        return $this;
    }

    public function getSituationFamiliale(): ?string
    {
        return $this->situationFamiliale;
    }

    public function setSituationFamiliale(?string $situationFamiliale): static
    {
        $this->situationFamiliale = $situationFamiliale;

        return $this;
    }

    public function isCasierJudiciaire(): ?bool
    {
        return $this->casierJudiciaire;
    }

    public function setCasierJudiciaire(?bool $casierJudiciaire): static
    {
        $this->casierJudiciaire = $casierJudiciaire;

        return $this;
    }

    public function getFortuneEstimee(): ?string
    {
        return $this->fortuneEstimee;
    }

    public function setFortuneEstimee(?string $fortuneEstimee): static
    {
        $this->fortuneEstimee = $fortuneEstimee;

        return $this;
    }
}
