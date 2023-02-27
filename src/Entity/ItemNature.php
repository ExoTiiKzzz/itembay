<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ItemNature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'itemNature', targetEntity: DefaultItem::class, orphanRemoval: true)]
    private Collection $defaultItems;

    public function __construct()
    {
        $this->defaultItems = new ArrayCollection();
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

    /**
     * @return Collection<int, DefaultItem>
     */
    public function getDefaultItems(): Collection
    {
        return $this->defaultItems;
    }

    public function addDefaultItem(DefaultItem $defaultItem): self
    {
        if (!$this->defaultItems->contains($defaultItem)) {
            $this->defaultItems->add($defaultItem);
            $defaultItem->setItemNature($this);
        }

        return $this;
    }

    public function removeDefaultItem(DefaultItem $defaultItem): self
    {
        if ($this->defaultItems->removeElement($defaultItem)) {
            // set the owning side to null (unless already changed)
            if ($defaultItem->getItemNature() === $this) {
                $defaultItem->setItemNature(null);
            }
        }

        return $this;
    }
}
