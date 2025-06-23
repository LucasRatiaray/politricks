<?php

namespace App\Entity;

use App\Repository\PartenaireMoralRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartenaireMoralRepository::class)]
class PartenaireMoral extends Partenaire
{
    #[ORM\Column(length: 255)]
    private ?string $raisonSociale = null;

    #[ORM\Column(length: 50)]
    private ?string $formeJuridique = null;

    #[ORM\Column(length: 14, nullable: true)]
    private ?string $siret = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $secteurActivite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dirigeantPrincipal = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $chiffreAffaires = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombreEmployes = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $paysFiscal = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateCreationEntreprise = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $capitalSocial = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function getRaisonSociale(): ?string
    {
        return $this->raisonSociale;
    }

    public function setRaisonSociale(string $raisonSociale): static
    {
        $this->raisonSociale = $raisonSociale;

        return $this;
    }

    public function getFormeJuridique(): ?string
    {
        return $this->formeJuridique;
    }

    public function setFormeJuridique(string $formeJuridique): static
    {
        $this->formeJuridique = $formeJuridique;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): static
    {
        $this->siret = $siret;

        return $this;
    }

    public function getSecteurActivite(): ?string
    {
        return $this->secteurActivite;
    }

    public function setSecteurActivite(?string $secteurActivite): static
    {
        $this->secteurActivite = $secteurActivite;

        return $this;
    }

    public function getDirigeantPrincipal(): ?string
    {
        return $this->dirigeantPrincipal;
    }

    public function setDirigeantPrincipal(?string $dirigeantPrincipal): static
    {
        $this->dirigeantPrincipal = $dirigeantPrincipal;

        return $this;
    }

    public function getChiffreAffaires(): ?string
    {
        return $this->chiffreAffaires;
    }

    public function setChiffreAffaires(?string $chiffreAffaires): static
    {
        $this->chiffreAffaires = $chiffreAffaires;

        return $this;
    }

    public function getNombreEmployes(): ?int
    {
        return $this->nombreEmployes;
    }

    public function setNombreEmployes(?int $nombreEmployes): static
    {
        $this->nombreEmployes = $nombreEmployes;

        return $this;
    }

    public function getPaysFiscal(): ?string
    {
        return $this->paysFiscal;
    }

    public function setPaysFiscal(?string $paysFiscal): static
    {
        $this->paysFiscal = $paysFiscal;

        return $this;
    }

    public function getDateCreationEntreprise(): ?\DateTime
    {
        return $this->dateCreationEntreprise;
    }

    public function setDateCreationEntreprise(?\DateTime $dateCreationEntreprise): static
    {
        $this->dateCreationEntreprise = $dateCreationEntreprise;

        return $this;
    }

    public function getCapitalSocial(): ?string
    {
        return $this->capitalSocial;
    }

    public function setCapitalSocial(?string $capitalSocial): static
    {
        $this->capitalSocial = $capitalSocial;

        return $this;
    }
}
