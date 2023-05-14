<?php

namespace App\Entity;

use App\Repository\ItemSetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemSetRepository::class)]
class ItemSet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $ankamaId = null;

    #[ORM\OneToMany(mappedBy: 'itemSet', targetEntity: DefaultItem::class)]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAnkamaId(): ?int
    {
        return $this->ankamaId;
    }

    public function setAnkamaId(int $ankamaId): self
    {
        $this->ankamaId = $ankamaId;

        return $this;
    }

    /**
     * @return Collection<int, DefaultItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(DefaultItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setItemSet($this);
        }

        return $this;
    }

    public function removeItem(DefaultItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getItemSet() === $this) {
                $item->setItemSet(null);
            }
        }

        return $this;
    }
}
