<?php

namespace App\Entity;

use App\Enum\DelitGraviteEnum;
use App\Enum\DelitStatutEnum;
use App\Enum\DelitTypeEnum;
use App\Repository\DelitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DelitRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap([
    'delit' => Delit::class,
    'financier' => DelitFinancier::class,
    'fraude' => DelitFraude::class,
    'vol' => DelitVol::class
])]
class Delit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: DelitTypeEnum::class)]
    private ?DelitTypeEnum $type = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTime $date = null;

    #[ORM\Column(enumType: DelitStatutEnum::class)]
    private ?DelitStatutEnum $statut = null;

    #[ORM\Column(enumType: DelitGraviteEnum::class)]
    private ?DelitGraviteEnum $gravite = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateDeclaration = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $numeroAffaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $procureurResponsable = null;

    #[ORM\Column(nullable: true)]
    private ?array $temoinsPrincipaux = null;

    #[ORM\Column(nullable: true)]
    private ?array $preuvesPrincipales = null;

    #[ORM\ManyToOne(inversedBy: 'delits')]
    private ?Lieu $lieu = null;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'delit')]
    private Collection $commentaires;

    /**
     * @var Collection<int, Document>
     */
    #[ORM\OneToMany(targetEntity: Document::class, mappedBy: 'delit')]
    private Collection $documents;

    /**
     * @var Collection<int, Politicien>
     */
    #[ORM\ManyToMany(targetEntity: Politicien::class, mappedBy: 'delits')]
    private Collection $politiciens;

    /**
     * @var Collection<int, Partenaire>
     */
    #[ORM\ManyToMany(targetEntity: Partenaire::class, inversedBy: 'delits')]
    private Collection $partenaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->politiciens = new ArrayCollection();
        $this->partenaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?DelitTypeEnum
    {
        return $this->type;
    }

    public function setType(DelitTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStatut(): ?DelitStatutEnum
    {
        return $this->statut;
    }

    public function setStatut(DelitStatutEnum $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getGravite(): ?DelitGraviteEnum
    {
        return $this->gravite;
    }

    public function setGravite(DelitGraviteEnum $gravite): static
    {
        $this->gravite = $gravite;

        return $this;
    }

    public function getDateDeclaration(): ?\DateTime
    {
        return $this->dateDeclaration;
    }

    public function setDateDeclaration(?\DateTime $dateDeclaration): static
    {
        $this->dateDeclaration = $dateDeclaration;

        return $this;
    }

    public function getNumeroAffaire(): ?string
    {
        return $this->numeroAffaire;
    }

    public function setNumeroAffaire(?string $numeroAffaire): static
    {
        $this->numeroAffaire = $numeroAffaire;

        return $this;
    }

    public function getProcureurResponsable(): ?string
    {
        return $this->procureurResponsable;
    }

    public function setProcureurResponsable(?string $procureurResponsable): static
    {
        $this->procureurResponsable = $procureurResponsable;

        return $this;
    }

    public function getTemoinsPrincipaux(): ?array
    {
        return $this->temoinsPrincipaux;
    }

    public function setTemoinsPrincipaux(?array $temoinsPrincipaux): static
    {
        $this->temoinsPrincipaux = $temoinsPrincipaux;

        return $this;
    }

    public function getPreuvesPrincipales(): ?array
    {
        return $this->preuvesPrincipales;
    }

    public function setPreuvesPrincipales(?array $preuvesPrincipales): static
    {
        $this->preuvesPrincipales = $preuvesPrincipales;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setDelit($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getDelit() === $this) {
                $commentaire->setDelit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setDelit($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getDelit() === $this) {
                $document->setDelit(null);
            }
        }

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
            $politicien->addDelit($this);
        }

        return $this;
    }

    public function removePoliticien(Politicien $politicien): static
    {
        if ($this->politiciens->removeElement($politicien)) {
            $politicien->removeDelit($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Partenaire>
     */
    public function getPartenaires(): Collection
    {
        return $this->partenaires;
    }

    public function addPartenaire(Partenaire $partenaire): static
    {
        if (!$this->partenaires->contains($partenaire)) {
            $this->partenaires->add($partenaire);
        }

        return $this;
    }

    public function removePartenaire(Partenaire $partenaire): static
    {
        $this->partenaires->removeElement($partenaire);

        return $this;
    }
}
