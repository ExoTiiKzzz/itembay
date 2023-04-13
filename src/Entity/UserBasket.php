<?php

namespace App\Entity;

use App\Repository\UserBasketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserBasketRepository::class)]
class UserBasket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'basket', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Item::class)]
    private Collection $items;

    #[ORM\ManyToMany(targetEntity: Batch::class, mappedBy: 'baskets')]
    private Collection $batches;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->batches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
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
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        $this->items->removeElement($item);

        return $this;
    }

    /**
     * @return Collection<int, Batch>
     */
    public function getBatches(): Collection
    {
        return $this->batches;
    }

    public function addBatch(Batch $batch): self
    {
        if (!$this->batches->contains($batch)) {
            $this->batches->add($batch);
            $batch->addBasket($this);
        }

        return $this;
    }

    public function removeBatch(Batch $batch): self
    {
        if ($this->batches->removeElement($batch)) {
            $batch->removeBasket($this);
        }

        return $this;
    }
}
