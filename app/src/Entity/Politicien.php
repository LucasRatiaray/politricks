<?php

namespace App\Entity;

use App\Repository\PoliticienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PoliticienRepository::class)]
class Politicien extends User
{
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $biographie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fonction = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateEntreePolitique = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mandatActuel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $circonscription = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $salaireMensuel = null;

    #[ORM\Column(nullable: true)]
    private ?array $declarationPatrimoine = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $casierJudiciaire = null;

    #[ORM\ManyToOne(inversedBy: 'politiciens')]
    private ?Parti $parti = null;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'auteur')]
    private Collection $commentaires;

    /**
     * @var Collection<int, Document>
     */
    #[ORM\OneToMany(targetEntity: Document::class, mappedBy: 'auteur')]
    private Collection $documentsAuteur;

    /**
     * @var Collection<int, Delit>
     */
    #[ORM\ManyToMany(targetEntity: Delit::class, inversedBy: 'politiciens')]
    private Collection $delits;

    public function __construct()
    {
        parent::__construct();
        $this->commentaires = new ArrayCollection();
        $this->documentsAuteur = new ArrayCollection();
        $this->delits = new ArrayCollection();
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(?string $biographie): static
    {
        $this->biographie = $biographie;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(?string $fonction): static
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getDateEntreePolitique(): ?\DateTime
    {
        return $this->dateEntreePolitique;
    }

    public function setDateEntreePolitique(?\DateTime $dateEntreePolitique): static
    {
        $this->dateEntreePolitique = $dateEntreePolitique;

        return $this;
    }

    public function getMandatActuel(): ?string
    {
        return $this->mandatActuel;
    }

    public function setMandatActuel(?string $mandatActuel): static
    {
        $this->mandatActuel = $mandatActuel;

        return $this;
    }

    public function getCirconscription(): ?string
    {
        return $this->circonscription;
    }

    public function setCirconscription(?string $circonscription): static
    {
        $this->circonscription = $circonscription;

        return $this;
    }

    public function getSalaireMensuel(): ?string
    {
        return $this->salaireMensuel;
    }

    public function setSalaireMensuel(?string $salaireMensuel): static
    {
        $this->salaireMensuel = $salaireMensuel;

        return $this;
    }

    public function getDeclarationPatrimoine(): ?array
    {
        return $this->declarationPatrimoine;
    }

    public function setDeclarationPatrimoine(?array $declarationPatrimoine): static
    {
        $this->declarationPatrimoine = $declarationPatrimoine;

        return $this;
    }

    public function getCasierJudiciaire(): ?string
    {
        return $this->casierJudiciaire;
    }

    public function setCasierJudiciaire(?string $casierJudiciaire): static
    {
        $this->casierJudiciaire = $casierJudiciaire;

        return $this;
    }

    public function getParti(): ?Parti
    {
        return $this->parti;
    }

    public function setParti(?Parti $parti): static
    {
        $this->parti = $parti;

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
            $commentaire->setAuteur($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getAuteur() === $this) {
                $commentaire->setAuteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocumentsAuteur(): Collection
    {
        return $this->documentsAuteur;
    }

    public function addDocumentsAuteur(Document $documentsAuteur): static
    {
        if (!$this->documentsAuteur->contains($documentsAuteur)) {
            $this->documentsAuteur->add($documentsAuteur);
            $documentsAuteur->setAuteur($this);
        }

        return $this;
    }

    public function removeDocumentsAuteur(Document $documentsAuteur): static
    {
        if ($this->documentsAuteur->removeElement($documentsAuteur)) {
            // set the owning side to null (unless already changed)
            if ($documentsAuteur->getAuteur() === $this) {
                $documentsAuteur->setAuteur(null);
            }
        }

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
        }

        return $this;
    }

    public function removeDelit(Delit $delit): static
    {
        $this->delits->removeElement($delit);

        return $this;
    }
}
