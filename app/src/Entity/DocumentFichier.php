<?php

namespace App\Entity;

use App\Repository\DocumentFichierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentFichierRepository::class)]
class DocumentFichier extends Document
{

    #[ORM\Column(length: 50)]
    private ?string $typeFichier = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $formatFichier = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombrePages = null;

    #[ORM\Column(nullable: true)]
    private ?bool $estSigneNumeriquement = null;

    #[ORM\Column(nullable: true)]
    private ?array $signataires = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateSignature = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $autoriteSignature = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $numeroDocument = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $versionDocument = null;

    #[ORM\Column(nullable: true)]
    private ?bool $documentOriginal = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contenuExtrait = null;

    #[ORM\Column(nullable: true)]
    private ?bool $indexeRecherche = null;

    #[ORM\Column(nullable: true)]
    private ?array $motsClesDocument = null;

    #[ORM\Column(nullable: true)]
    private ?array $clausesImportantes = null;

    #[ORM\Column(nullable: true)]
    private ?array $montantsMentionnes = null;

    #[ORM\Column(nullable: true)]
    private ?array $personnesMentionnees = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateValidite = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function getTypeFichier(): ?string
    {
        return $this->typeFichier;
    }

    public function setTypeFichier(string $typeFichier): static
    {
        $this->typeFichier = $typeFichier;

        return $this;
    }

    public function getFormatFichier(): ?string
    {
        return $this->formatFichier;
    }

    public function setFormatFichier(?string $formatFichier): static
    {
        $this->formatFichier = $formatFichier;

        return $this;
    }

    public function getNombrePages(): ?int
    {
        return $this->nombrePages;
    }

    public function setNombrePages(?int $nombrePages): static
    {
        $this->nombrePages = $nombrePages;

        return $this;
    }

    public function isEstSigneNumeriquement(): ?bool
    {
        return $this->estSigneNumeriquement;
    }

    public function setEstSigneNumeriquement(?bool $estSigneNumeriquement): static
    {
        $this->estSigneNumeriquement = $estSigneNumeriquement;

        return $this;
    }

    public function getSignataires(): ?array
    {
        return $this->signataires;
    }

    public function setSignataires(?array $signataires): static
    {
        $this->signataires = $signataires;

        return $this;
    }

    public function getDateSignature(): ?\DateTime
    {
        return $this->dateSignature;
    }

    public function setDateSignature(?\DateTime $dateSignature): static
    {
        $this->dateSignature = $dateSignature;

        return $this;
    }

    public function getAutoriteSignature(): ?string
    {
        return $this->autoriteSignature;
    }

    public function setAutoriteSignature(?string $autoriteSignature): static
    {
        $this->autoriteSignature = $autoriteSignature;

        return $this;
    }

    public function getNumeroDocument(): ?string
    {
        return $this->numeroDocument;
    }

    public function setNumeroDocument(?string $numeroDocument): static
    {
        $this->numeroDocument = $numeroDocument;

        return $this;
    }

    public function getVersionDocument(): ?string
    {
        return $this->versionDocument;
    }

    public function setVersionDocument(?string $versionDocument): static
    {
        $this->versionDocument = $versionDocument;

        return $this;
    }

    public function isDocumentOriginal(): ?bool
    {
        return $this->documentOriginal;
    }

    public function setDocumentOriginal(?bool $documentOriginal): static
    {
        $this->documentOriginal = $documentOriginal;

        return $this;
    }

    public function getContenuExtrait(): ?string
    {
        return $this->contenuExtrait;
    }

    public function setContenuExtrait(?string $contenuExtrait): static
    {
        $this->contenuExtrait = $contenuExtrait;

        return $this;
    }

    public function isIndexeRecherche(): ?bool
    {
        return $this->indexeRecherche;
    }

    public function setIndexeRecherche(?bool $indexeRecherche): static
    {
        $this->indexeRecherche = $indexeRecherche;

        return $this;
    }

    public function getMotsClesDocument(): ?array
    {
        return $this->motsClesDocument;
    }

    public function setMotsClesDocument(?array $motsClesDocument): static
    {
        $this->motsClesDocument = $motsClesDocument;

        return $this;
    }

    public function getClausesImportantes(): ?array
    {
        return $this->clausesImportantes;
    }

    public function setClausesImportantes(?array $clausesImportantes): static
    {
        $this->clausesImportantes = $clausesImportantes;

        return $this;
    }

    public function getMontantsMentionnes(): ?array
    {
        return $this->montantsMentionnes;
    }

    public function setMontantsMentionnes(?array $montantsMentionnes): static
    {
        $this->montantsMentionnes = $montantsMentionnes;

        return $this;
    }

    public function getPersonnesMentionnees(): ?array
    {
        return $this->personnesMentionnees;
    }

    public function setPersonnesMentionnees(?array $personnesMentionnees): static
    {
        $this->personnesMentionnees = $personnesMentionnees;

        return $this;
    }

    public function getDateValidite(): ?\DateTime
    {
        return $this->dateValidite;
    }

    public function setDateValidite(?\DateTime $dateValidite): static
    {
        $this->dateValidite = $dateValidite;

        return $this;
    }
}
