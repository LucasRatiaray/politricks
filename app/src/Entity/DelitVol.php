<?php

namespace App\Entity;

use App\Repository\DelitVolRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DelitVolRepository::class)]
class DelitVol extends Delit
{
    #[ORM\Column(nullable: true)]
    private ?array $biensDerobes = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $valeurEstimee = null;

    #[ORM\Column(nullable: true)]
    private ?bool $biensRecuperes = null;

    #[ORM\Column(nullable: true)]
    private ?int $pourcentageRecupere = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieuStockage = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $methodeDerriereVol = null;

    #[ORM\Column(nullable: true)]
    private ?array $receleurs = null;

    #[ORM\Column(nullable: true)]
    private ?bool $volPremedite = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function getBiensDerobes(): ?array
    {
        return $this->biensDerobes;
    }

    public function setBiensDerobes(?array $biensDerobes): static
    {
        $this->biensDerobes = $biensDerobes;

        return $this;
    }

    public function getValeurEstimee(): ?string
    {
        return $this->valeurEstimee;
    }

    public function setValeurEstimee(?string $valeurEstimee): static
    {
        $this->valeurEstimee = $valeurEstimee;

        return $this;
    }

    public function isBiensRecuperes(): ?bool
    {
        return $this->biensRecuperes;
    }

    public function setBiensRecuperes(?bool $biensRecuperes): static
    {
        $this->biensRecuperes = $biensRecuperes;

        return $this;
    }

    public function getPourcentageRecupere(): ?int
    {
        return $this->pourcentageRecupere;
    }

    public function setPourcentageRecupere(?int $pourcentageRecupere): static
    {
        $this->pourcentageRecupere = $pourcentageRecupere;

        return $this;
    }

    public function getLieuStockage(): ?string
    {
        return $this->lieuStockage;
    }

    public function setLieuStockage(?string $lieuStockage): static
    {
        $this->lieuStockage = $lieuStockage;

        return $this;
    }

    public function getMethodeDerriereVol(): ?string
    {
        return $this->methodeDerriereVol;
    }

    public function setMethodeDerriereVol(?string $methodeDerriereVol): static
    {
        $this->methodeDerriereVol = $methodeDerriereVol;

        return $this;
    }

    public function getReceleurs(): ?array
    {
        return $this->receleurs;
    }

    public function setReceleurs(?array $receleurs): static
    {
        $this->receleurs = $receleurs;

        return $this;
    }

    public function isVolPremedite(): ?bool
    {
        return $this->volPremedite;
    }

    public function setVolPremedite(?bool $volPremedite): static
    {
        $this->volPremedite = $volPremedite;

        return $this;
    }
}
