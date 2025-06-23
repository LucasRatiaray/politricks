<?php

namespace App\Entity;

use App\Enum\DelitFinancierDeviseEnum;
use App\Repository\DelitFinancierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DelitFinancierRepository::class)]
class DelitFinancier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $montantEstime = null;

    #[ORM\Column(enumType: DelitFinancierDeviseEnum::class)]
    private ?DelitFinancierDeviseEnum $dedevise = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $methodePaiement = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $compteBancaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paradissFiscal = null;

    #[ORM\Column(nullable: true)]
    private ?bool $blanchimentSoupçonne = null;

    #[ORM\Column(nullable: true)]
    private ?array $institutionsImpliquees = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $circuitFinancier = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $montantRecupere = null;

    #[ORM\Column(nullable: true)]
    private ?bool $argentRecupere = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantEstime(): ?string
    {
        return $this->montantEstime;
    }

    public function setMontantEstime(?string $montantEstime): static
    {
        $this->montantEstime = $montantEstime;

        return $this;
    }

    public function getDedevise(): ?DelitFinancierDeviseEnum
    {
        return $this->dedevise;
    }

    public function setDedevise(DelitFinancierDeviseEnum $dedevise): static
    {
        $this->dedevise = $dedevise;

        return $this;
    }

    public function getMethodePaiement(): ?string
    {
        return $this->methodePaiement;
    }

    public function setMethodePaiement(?string $methodePaiement): static
    {
        $this->methodePaiement = $methodePaiement;

        return $this;
    }

    public function getCompteBancaire(): ?string
    {
        return $this->compteBancaire;
    }

    public function setCompteBancaire(?string $compteBancaire): static
    {
        $this->compteBancaire = $compteBancaire;

        return $this;
    }

    public function getParadissFiscal(): ?string
    {
        return $this->paradissFiscal;
    }

    public function setParadissFiscal(?string $paradissFiscal): static
    {
        $this->paradissFiscal = $paradissFiscal;

        return $this;
    }

    public function isBlanchimentSoupçonne(): ?bool
    {
        return $this->blanchimentSoupçonne;
    }

    public function setBlanchimentSoupçonne(?bool $blanchimentSoupçonne): static
    {
        $this->blanchimentSoupçonne = $blanchimentSoupçonne;

        return $this;
    }

    public function getInstitutionsImpliquees(): ?array
    {
        return $this->institutionsImpliquees;
    }

    public function setInstitutionsImpliquees(?array $institutionsImpliquees): static
    {
        $this->institutionsImpliquees = $institutionsImpliquees;

        return $this;
    }

    public function getCircuitFinancier(): ?string
    {
        return $this->circuitFinancier;
    }

    public function setCircuitFinancier(?string $circuitFinancier): static
    {
        $this->circuitFinancier = $circuitFinancier;

        return $this;
    }

    public function getMontantRecupere(): ?string
    {
        return $this->montantRecupere;
    }

    public function setMontantRecupere(?string $montantRecupere): static
    {
        $this->montantRecupere = $montantRecupere;

        return $this;
    }

    public function isArgentRecupere(): ?bool
    {
        return $this->argentRecupere;
    }

    public function setArgentRecupere(?bool $argentRecupere): static
    {
        $this->argentRecupere = $argentRecupere;

        return $this;
    }
}
