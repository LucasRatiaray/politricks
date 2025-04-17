<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Delit>
     */
    #[ORM\OneToMany(targetEntity: Delit::class, mappedBy: 'utilisateur_id')]
    private Collection $delits;

    /**
     * @var Collection<int, DelitComplice>
     */
    #[ORM\ManyToMany(targetEntity: DelitComplice::class, mappedBy: 'user_id')]
    private Collection $delitComplices;

    /**
     * @var Collection<int, UserTypedelit>
     */
    #[ORM\ManyToMany(targetEntity: UserTypedelit::class, mappedBy: 'user_id')]
    private Collection $userTypedelits;

    public function __construct()
    {
        $this->delits = new ArrayCollection();
        $this->delitComplices = new ArrayCollection();
        $this->userTypedelits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $delit->setUtilisateurId($this);
        }

        return $this;
    }

    public function removeDelit(Delit $delit): static
    {
        if ($this->delits->removeElement($delit)) {
            // set the owning side to null (unless already changed)
            if ($delit->getUtilisateurId() === $this) {
                $delit->setUtilisateurId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DelitComplice>
     */
    public function getDelitComplices(): Collection
    {
        return $this->delitComplices;
    }

    public function addDelitComplice(DelitComplice $delitComplice): static
    {
        if (!$this->delitComplices->contains($delitComplice)) {
            $this->delitComplices->add($delitComplice);
            $delitComplice->addUserId($this);
        }

        return $this;
    }

    public function removeDelitComplice(DelitComplice $delitComplice): static
    {
        if ($this->delitComplices->removeElement($delitComplice)) {
            $delitComplice->removeUserId($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, UserTypedelit>
     */
    public function getUserTypedelits(): Collection
    {
        return $this->userTypedelits;
    }

    public function addUserTypedelit(UserTypedelit $userTypedelit): static
    {
        if (!$this->userTypedelits->contains($userTypedelit)) {
            $this->userTypedelits->add($userTypedelit);
            $userTypedelit->addUserId($this);
        }

        return $this;
    }

    public function removeUserTypedelit(UserTypedelit $userTypedelit): static
    {
        if ($this->userTypedelits->removeElement($userTypedelit)) {
            $userTypedelit->removeUserId($this);
        }

        return $this;
    }
}
