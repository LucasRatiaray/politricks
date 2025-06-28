<?php

namespace App\Entity;

use App\Enum\DocumentLangueDocumentEnum;
use App\Enum\DocumentNiveauConfidentialiteEnum;
use App\Repository\DocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap([
    'document' => Document::class,
    'image' => DocumentImage::class,
    'video' => DocumentVideo::class,
    'audio' => DocumentAudio::class,
    'fichier' => DocumentFichier::class
])]
class Document
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 500)]
    private ?string $chemin = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $tailleFichier = null;

    #[ORM\Column(enumType: DocumentNiveauConfidentialiteEnum::class)]
    private ?DocumentNiveauConfidentialiteEnum $niveauConfidentialite = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateDeclassification = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sourceInformation = null;

    #[ORM\Column(nullable: true)]
    private ?array $personnesAutorisees = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombreConsultations = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $derniereConsultation = null;

    #[ORM\Column]
    private ?bool $estArchive = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $checksum = null;

    #[ORM\Column(nullable: true)]
    private ?array $motsCles = null;

    #[ORM\Column(enumType: DocumentLangueDocumentEnum::class)]
    private ?DocumentLangueDocumentEnum $langueDocument = null;

    #[ORM\ManyToOne(inversedBy: 'documentsAuteur')]
    private ?Politicien $auteur = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    private ?Delit $delit = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'documentsEnfants')]
    #[ORM\JoinColumn(nullable: true)]
    private ?self $documentParent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'documentParent', orphanRemoval: true)]
    private Collection $documentsEnfants;

    public function __construct()
    {
        $this->documentsEnfants = new ArrayCollection();
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

    public function getChemin(): ?string
    {
        return $this->chemin;
    }

    public function setChemin(string $chemin): static
    {
        $this->chemin = $chemin;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTailleFichier(): ?string
    {
        return $this->tailleFichier;
    }

    public function setTailleFichier(?string $tailleFichier): static
    {
        $this->tailleFichier = $tailleFichier;

        return $this;
    }

    public function getNiveauConfidentialite(): ?DocumentNiveauConfidentialiteEnum
    {
        return $this->niveauConfidentialite;
    }

    public function setNiveauConfidentialite(DocumentNiveauConfidentialiteEnum $niveauConfidentialite): static
    {
        $this->niveauConfidentialite = $niveauConfidentialite;

        return $this;
    }

    public function getDateDeclassification(): ?\DateTime
    {
        return $this->dateDeclassification;
    }

    public function setDateDeclassification(?\DateTime $dateDeclassification): static
    {
        $this->dateDeclassification = $dateDeclassification;

        return $this;
    }

    public function getSourceInformation(): ?string
    {
        return $this->sourceInformation;
    }

    public function setSourceInformation(?string $sourceInformation): static
    {
        $this->sourceInformation = $sourceInformation;

        return $this;
    }

    public function getPersonnesAutorisees(): ?array
    {
        return $this->personnesAutorisees;
    }

    public function setPersonnesAutorisees(?array $personnesAutorisees): static
    {
        $this->personnesAutorisees = $personnesAutorisees;

        return $this;
    }

    public function getNombreConsultations(): ?int
    {
        return $this->nombreConsultations;
    }

    public function setNombreConsultations(?int $nombreConsultations): static
    {
        $this->nombreConsultations = $nombreConsultations;

        return $this;
    }

    public function getDerniereConsultation(): ?\DateTime
    {
        return $this->derniereConsultation;
    }

    public function setDerniereConsultation(?\DateTime $derniereConsultation): static
    {
        $this->derniereConsultation = $derniereConsultation;

        return $this;
    }

    public function isEstArchive(): ?bool
    {
        return $this->estArchive;
    }

    public function setEstArchive(bool $estArchive): static
    {
        $this->estArchive = $estArchive;

        return $this;
    }

    public function getChecksum(): ?string
    {
        return $this->checksum;
    }

    public function setChecksum(?string $checksum): static
    {
        $this->checksum = $checksum;

        return $this;
    }

    public function getMotsCles(): ?array
    {
        return $this->motsCles;
    }

    public function setMotsCles(?array $motsCles): static
    {
        $this->motsCles = $motsCles;

        return $this;
    }

    public function getLangueDocument(): ?DocumentLangueDocumentEnum
    {
        return $this->langueDocument;
    }

    public function setLangueDocument(DocumentLangueDocumentEnum $langueDocument): static
    {
        $this->langueDocument = $langueDocument;

        return $this;
    }

    public function getAuteur(): ?Politicien
    {
        return $this->auteur;
    }

    public function setAuteur(?Politicien $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getDelit(): ?Delit
    {
        return $this->delit;
    }

    public function setDelit(?Delit $delit): static
    {
        $this->delit = $delit;

        return $this;
    }

    public function getDocumentParent(): ?self
    {
        return $this->documentParent;
    }

    public function setDocumentParent(?self $documentParent): static
    {
        $this->documentParent = $documentParent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getDocumentsEnfants(): Collection
    {
        return $this->documentsEnfants;
    }

    public function addDocumentsEnfant(self $documentsEnfant): static
    {
        if (!$this->documentsEnfants->contains($documentsEnfant)) {
            $this->documentsEnfants->add($documentsEnfant);
            $documentsEnfant->setDocumentParent($this);
        }

        return $this;
    }

    public function removeDocumentsEnfant(self $documentsEnfant): static
    {
        if ($this->documentsEnfants->removeElement($documentsEnfant)) {
            // set the owning side to null (unless already changed)
            if ($documentsEnfant->getDocumentParent() === $this) {
                $documentsEnfant->setDocumentParent(null);
            }
        }

        return $this;
    }
}
