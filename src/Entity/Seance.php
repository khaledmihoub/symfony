<?php

namespace App\Entity;

use App\Repository\SeanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeanceRepository::class)]
class Seance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etat = null;

    #[ORM\Column(length: 255)]
    private ?string $durée = null;

    #[ORM\ManyToOne(inversedBy: 'seances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $client_id = null;

    #[ORM\OneToOne(mappedBy: 'seance', cascade: ['persist', 'remove'])]
    private ?Emplacement $emplacement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDurée(): ?string
    {
        return $this->durée;
    }

    public function setDurée(string $durée): self
    {
        $this->durée = $durée;

        return $this;
    }

    public function getClientId(): ?user
    {
        return $this->client_id;
    }

    public function setClientId(?user $client_id): self
    {
        $this->client_id = $client_id;

        return $this;
    }

    public function getEmplacement(): ?Emplacement
    {
        return $this->emplacement;
    }

    public function setEmplacement(Emplacement $emplacement): self
    {
        // set the owning side of the relation if necessary
        if ($emplacement->getSeance() !== $this) {
            $emplacement->setSeance($this);
        }

        $this->emplacement = $emplacement;

        return $this;
    }
}
