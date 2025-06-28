<?php

namespace App\Entity;

use App\Repository\PartiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartiRepository::class)]
class Parti
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $couleur = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $slogan = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siteWeb = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adresseSiege = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephoneContact = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailContact = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $orientationPolitique = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $budgetAnnuel = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombreAdherents = null;

    #[ORM\Column(nullable: true)]
    private ?bool $partiActif = null;

    /**
     * @var Collection<int, Politicien>
     */
    #[ORM\OneToMany(targetEntity: Politicien::class, mappedBy: 'parti')]
    private Collection $politiciens;

    public function __construct()
    {
        $this->politiciens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getSlogan(): ?string
    {
        return $this->slogan;
    }

    public function setSlogan(?string $slogan): static
    {
        $this->slogan = $slogan;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getSiteWeb(): ?string
    {
        return $this->siteWeb;
    }

    public function setSiteWeb(?string $siteWeb): static
    {
        $this->siteWeb = $siteWeb;

        return $this;
    }

    public function getAdresseSiege(): ?string
    {
        return $this->adresseSiege;
    }

    public function setAdresseSiege(?string $adresseSiege): static
    {
        $this->adresseSiege = $adresseSiege;

        return $this;
    }

    public function getTelephoneContact(): ?string
    {
        return $this->telephoneContact;
    }

    public function setTelephoneContact(?string $telephoneContact): static
    {
        $this->telephoneContact = $telephoneContact;

        return $this;
    }

    public function getEmailContact(): ?string
    {
        return $this->emailContact;
    }

    public function setEmailContact(?string $emailContact): static
    {
        $this->emailContact = $emailContact;

        return $this;
    }

    public function getOrientationPolitique(): ?string
    {
        return $this->orientationPolitique;
    }

    public function setOrientationPolitique(?string $orientationPolitique): static
    {
        $this->orientationPolitique = $orientationPolitique;

        return $this;
    }

    public function getBudgetAnnuel(): ?string
    {
        return $this->budgetAnnuel;
    }

    public function setBudgetAnnuel(?string $budgetAnnuel): static
    {
        $this->budgetAnnuel = $budgetAnnuel;

        return $this;
    }

    public function getNombreAdherents(): ?int
    {
        return $this->nombreAdherents;
    }

    public function setNombreAdherents(?int $nombreAdherents): static
    {
        $this->nombreAdherents = $nombreAdherents;

        return $this;
    }

    public function isPartiActif(): ?bool
    {
        return $this->partiActif;
    }

    public function setPartiActif(?bool $partiActif): static
    {
        $this->partiActif = $partiActif;

        return $this;
    }

    /**
     * @return Collection<int, Politicien>
     */
    public function getPoliticiens(): Collection
    {
        return $this->politiciens;
    }

    public function addPoliticien(Politicien $politicien): static
    {
        if (!$this->politiciens->contains($politicien)) {
            $this->politiciens->add($politicien);
            $politicien->setParti($this);
        }

        return $this;
    }

    public function removePoliticien(Politicien $politicien): static
    {
        if ($this->politiciens->removeElement($politicien)) {
            // set the owning side to null (unless already changed)
            if ($politicien->getParti() === $this) {
                $politicien->setParti(null);
            }
        }

        return $this;
    }
}
