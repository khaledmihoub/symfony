<?php

namespace App\Entity;

use App\Repository\EmplacementChoixRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmplacementChoixRepository::class)]
class EmplacementChoix
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $governorat = null;

    #[ORM\Column(length: 255)]
    private ?string $delegation = null;

    #[ORM\Column(length: 255)]
    private ?string $localite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGovernorat(): ?string
    {
        return $this->governorat;
    }

    public function setGovernorat(string $governorat): self
    {
        $this->governorat = $governorat;

        return $this;
    }

    public function getDelegation(): ?string
    {
        return $this->delegation;
    }

    public function setDelegation(string $delegation): self
    {
        $this->delegation = $delegation;

        return $this;
    }

    public function getLocalite(): ?string
    {
        return $this->localite;
    }

    public function setLocalite(string $localite): self
    {
        $this->localite = $localite;

        return $this;
    }
}
