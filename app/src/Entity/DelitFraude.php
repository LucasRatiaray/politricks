<?php

namespace App\Entity;

use App\Enum\DelitFraudeMethodeFraudeEnum;
use App\Enum\DelitFraudeTypeFraudeEnum;
use App\Repository\DelitFraudeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DelitFraudeRepository::class)]
class DelitFraude extends Delit
{
    #[ORM\Column(enumType: DelitFraudeTypeFraudeEnum::class)]
    private ?DelitFraudeTypeFraudeEnum $typeFraude = null;

    #[ORM\Column(nullable: true)]
    private ?array $documentsManipules = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombreVictimes = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $prejudiceEstime = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, enumType: DelitFraudeMethodeFraudeEnum::class)]
    private array $methodeFraude = [];

    #[ORM\Column(nullable: true)]
    private ?array $complicesIdentifies = null;

    #[ORM\Column(nullable: true)]
    private ?bool $systemeInformatique = null;

    #[ORM\Column(nullable: true)]
    private ?bool $fraudeOrganisee = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function getTypeFraude(): ?DelitFraudeTypeFraudeEnum
    {
        return $this->typeFraude;
    }

    public function setTypeFraude(DelitFraudeTypeFraudeEnum $typeFraude): static
    {
        $this->typeFraude = $typeFraude;

        return $this;
    }

    public function getDocumentsManipules(): ?array
    {
        return $this->documentsManipules;
    }

    public function setDocumentsManipules(?array $documentsManipules): static
    {
        $this->documentsManipules = $documentsManipules;

        return $this;
    }

    public function getNombreVictimes(): ?int
    {
        return $this->nombreVictimes;
    }

    public function setNombreVictimes(?int $nombreVictimes): static
    {
        $this->nombreVictimes = $nombreVictimes;

        return $this;
    }

    public function getPrejudiceEstime(): ?string
    {
        return $this->prejudiceEstime;
    }

    public function setPrejudiceEstime(?string $prejudiceEstime): static
    {
        $this->prejudiceEstime = $prejudiceEstime;

        return $this;
    }

    /**
     * @return DelitFraudeMethodeFraudeEnum[]
     */
    public function getMethodeFraude(): array
    {
        return $this->methodeFraude;
    }

    public function setMethodeFraude(array $methodeFraude): static
    {
        $this->methodeFraude = $methodeFraude;

        return $this;
    }

    public function getComplicesIdentifies(): ?array
    {
        return $this->complicesIdentifies;
    }

    public function setComplicesIdentifies(?array $complicesIdentifies): static
    {
        $this->complicesIdentifies = $complicesIdentifies;

        return $this;
    }

    public function isSystemeInformatique(): ?bool
    {
        return $this->systemeInformatique;
    }

    public function setSystemeInformatique(?bool $systemeInformatique): static
    {
        $this->systemeInformatique = $systemeInformatique;

        return $this;
    }

    public function isFraudeOrganisee(): ?bool
    {
        return $this->fraudeOrganisee;
    }

    public function setFraudeOrganisee(?bool $fraudeOrganisee): static
    {
        $this->fraudeOrganisee = $fraudeOrganisee;

        return $this;
    }
}
