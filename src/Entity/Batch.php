<?php

namespace App\Entity;

use App\Repository\BatchRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BatchRepository::class)]
class Batch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'batch', targetEntity: Item::class)]
    private Collection $items;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\ManyToOne(inversedBy: 'batches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DefaultItem $defaultItem = null;

    #[ORM\ManyToOne(inversedBy: 'batches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToMany(targetEntity: UserBasket::class, inversedBy: 'batches')]
    private Collection $baskets;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->baskets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setBatch($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getBatch() === $this) {
                $item->setBatch(null);
            }
        }

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDefaultItem(): ?DefaultItem
    {
        return $this->defaultItem;
    }

    public function setDefaultItem(?DefaultItem $defaultItem): self
    {
        $this->defaultItem = $defaultItem;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return Collection<int, UserBasket>
     */
    public function getBaskets(): Collection
    {
        return $this->baskets;
    }

    public function addBasket(UserBasket $basket): self
    {
        if (!$this->baskets->contains($basket)) {
            $this->baskets->add($basket);
        }

        return $this;
    }

    public function removeBasket(UserBasket $basket): self
    {
        $this->baskets->removeElement($basket);

        return $this;
    }
}
