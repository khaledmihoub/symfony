<?php

namespace App\Entity;

use App\Repository\LigneDeCommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneDeCommandeRepository::class)]
class LigneDeCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ligneDeCommandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?produit $id_produit = null;

    #[ORM\ManyToOne(inversedBy: 'ligneDeCommandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?commande $id_commande = null;

    #[ORM\Column]
    private ?int $quantite ;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProduit(): ?produit
    {
        return $this->id_produit;
    }

    public function setIdProduit(?produit $id_produit): self
    {
        $this->id_produit = $id_produit;

        return $this;
    }

    public function getIdCommande(): ?commande
    {
        return $this->id_commande;
    }

    public function setIdCommande(?commande $id_commande): self
    {
        $this->id_commande = $id_commande;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }
}
