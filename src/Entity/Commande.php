<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?Client $client = null;

    /**
     * @var Collection<int, DetailCommande>
     */
    #[ORM\OneToMany(targetEntity: DetailCommande::class, mappedBy: 'commande')]
    private Collection $details;

    public function __construct()
    {
        $this->details = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, DetailCommande>
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(DetailCommande $detail): static
    {
        if (!$this->details->contains($detail)) {
            $this->details->add($detail);
            $detail->setCommande($this);
        }

        return $this;
    }

    public function removeDetail(DetailCommande $detail): static
    {
        if ($this->details->removeElement($detail)) {
            // set the owning side to null (unless already changed)
            if ($detail->getCommande() === $this) {
                $detail->setCommande(null);
            }
        }

        return $this;
    }

    // Ajout de la méthode pour ajouter un article à la commande
    public function addArticle(Article $article, float $prix, int $quantite): static
    {
        // Créer un nouveau détail de commande (DetailCommande)
        $detail = new DetailCommande();
        $detail->setArticle($article);
        $detail->setPrix($prix);
        $detail->setQuantite($quantite);
        
        // Ajouter le détail à la commande
        $this->addDetail($detail);

        return $this;
    }
}
