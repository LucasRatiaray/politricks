<?php

namespace App\Entity;

use App\Enum\PartenaireNiveauRisqueEnum;
use App\Repository\PartenaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartenaireRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap([
    'physique' => PartenairePhysique::class,
    'moral' => PartenaireMoral::class
])]
class Partenaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siteWeb = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(enumType: PartenaireNiveauRisqueEnum::class)]
    private ?PartenaireNiveauRisqueEnum $niveauRisque = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $pays = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $datePremiereCollaboration = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombreDelitsImplique = null;

    #[ORM\Column]
    private ?bool $estActif = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentairesInternes = null;

    /**
     * @var Collection<int, Delit>
     */
    #[ORM\ManyToMany(targetEntity: Delit::class, mappedBy: 'partenaires')]
    private Collection $delits;

    public function __construct()
    {
        $this->delits = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

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

    public function getNiveauRisque(): ?PartenaireNiveauRisqueEnum
    {
        return $this->niveauRisque;
    }

    public function setNiveauRisque(PartenaireNiveauRisqueEnum $niveauRisque): static
    {
        $this->niveauRisque = $niveauRisque;

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

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getDatePremiereCollaboration(): ?\DateTime
    {
        return $this->datePremiereCollaboration;
    }

    public function setDatePremiereCollaboration(?\DateTime $datePremiereCollaboration): static
    {
        $this->datePremiereCollaboration = $datePremiereCollaboration;

        return $this;
    }

    public function getNombreDelitsImplique(): ?int
    {
        return $this->nombreDelitsImplique;
    }

    public function setNombreDelitsImplique(?int $nombreDelitsImplique): static
    {
        $this->nombreDelitsImplique = $nombreDelitsImplique;

        return $this;
    }

    public function isEstActif(): ?bool
    {
        return $this->estActif;
    }

    public function setEstActif(?bool $estActif): static
    {
        $this->estActif = $estActif;

        return $this;
    }

    public function getCommentairesInternes(): ?string
    {
        return $this->commentairesInternes;
    }

    public function setCommentairesInternes(?string $commentairesInternes): static
    {
        $this->commentairesInternes = $commentairesInternes;

        return $this;
    }

    /**
     * @return Collection<int, Delit>
     */
    public function getDelits(): Collection
    {
        return $this->delits;
    }

    public function addDelit(Delit $delit): static
    {
        if (!$this->delits->contains($delit)) {
            $this->delits->add($delit);
            $delit->addPartenaire($this);
        }

        return $this;
    }

    public function removeDelit(Delit $delit): static
    {
        if ($this->delits->removeElement($delit)) {
            $delit->removePartenaire($this);
        }

        return $this;
    }
}
