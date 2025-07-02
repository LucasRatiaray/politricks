<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateModification = null;

    #[ORM\Column(nullable: true)]
    private ?bool $estModere = null;

    #[ORM\Column(nullable: true)]
    private ?int $scoreCredibilite = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $typeCommentaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $domaineExpertise = null;

    #[ORM\Column(nullable: true)]
    private ?bool $estPublic = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombreLikes = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombreDislikes = null;

    #[ORM\Column(nullable: true)]
    private ?bool $estSignale = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $raisonSignalement = null;

    #[ORM\ManyToOne]
    private ?User $auteur = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?Delit $delit = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'commentairesEnfants')]
    private ?self $commentaireParent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'commentaireParent')]
    private Collection $commentairesEnfants;

    public function __construct()
    {
        $this->commentairesEnfants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

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

    public function getDateModification(): ?\DateTime
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTime $dateModification): static
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    public function isEstModere(): ?bool
    {
        return $this->estModere;
    }

    public function setEstModere(?bool $estModere): static
    {
        $this->estModere = $estModere;

        return $this;
    }

    public function getScoreCredibilite(): ?int
    {
        return $this->scoreCredibilite;
    }

    public function setScoreCredibilite(?int $scoreCredibilite): static
    {
        $this->scoreCredibilite = $scoreCredibilite;

        return $this;
    }

    public function getTypeCommentaire(): ?string
    {
        return $this->typeCommentaire;
    }

    public function setTypeCommentaire(?string $typeCommentaire): static
    {
        $this->typeCommentaire = $typeCommentaire;

        return $this;
    }

    public function getDomaineExpertise(): ?string
    {
        return $this->domaineExpertise;
    }

    public function setDomaineExpertise(?string $domaineExpertise): static
    {
        $this->domaineExpertise = $domaineExpertise;

        return $this;
    }

    public function isEstPublic(): ?bool
    {
        return $this->estPublic;
    }

    public function setEstPublic(?bool $estPublic): static
    {
        $this->estPublic = $estPublic;

        return $this;
    }

    public function getNombreLikes(): ?int
    {
        return $this->nombreLikes;
    }

    public function setNombreLikes(?int $nombreLikes): static
    {
        $this->nombreLikes = $nombreLikes;

        return $this;
    }

    public function getNombreDislikes(): ?int
    {
        return $this->nombreDislikes;
    }

    public function setNombreDislikes(?int $nombreDislikes): static
    {
        $this->nombreDislikes = $nombreDislikes;

        return $this;
    }

    public function isEstSignale(): ?bool
    {
        return $this->estSignale;
    }

    public function setEstSignale(?bool $estSignale): static
    {
        $this->estSignale = $estSignale;

        return $this;
    }

    public function getRaisonSignalement(): ?string
    {
        return $this->raisonSignalement;
    }

    public function setRaisonSignalement(?string $raisonSignalement): static
    {
        $this->raisonSignalement = $raisonSignalement;

        return $this;
    }

    public function getAuteur(): ?User
    {
        return $this->auteur;
    }

    public function setAuteur(?User $auteur): static
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

    public function getCommentaireParent(): ?self
    {
        return $this->commentaireParent;
    }

    public function setCommentaireParent(?self $commentaireParent): static
    {
        $this->commentaireParent = $commentaireParent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCommentairesEnfants(): Collection
    {
        return $this->commentairesEnfants;
    }

    public function addCommentairesEnfant(self $commentairesEnfant): static
    {
        if (!$this->commentairesEnfants->contains($commentairesEnfant)) {
            $this->commentairesEnfants->add($commentairesEnfant);
            $commentairesEnfant->setCommentaireParent($this);
        }

        return $this;
    }

    public function removeCommentairesEnfant(self $commentairesEnfant): static
    {
        if ($this->commentairesEnfants->removeElement($commentairesEnfant)) {
            // set the owning side to null (unless already changed)
            if ($commentairesEnfant->getCommentaireParent() === $this) {
                $commentairesEnfant->setCommentaireParent(null);
            }
        }

        return $this;
    }
}
