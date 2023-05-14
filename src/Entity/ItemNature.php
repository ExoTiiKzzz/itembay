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

    #[ORM\OneToMany(mappedBy: 'itemNature', targetEntity: ItemType::class)]
    private Collection $itemTypes;

    #[ORM\Column]
    private ?int $ankamaId = null;

    public function __construct()
    {
        $this->defaultItems = new ArrayCollection();
        $this->itemTypes = new ArrayCollection();
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

    /**
     * @return Collection<int, ItemType>
     */
    public function getItemTypes(): Collection
    {
        return $this->itemTypes;
    }

    public function addItemType(ItemType $itemType): self
    {
        if (!$this->itemTypes->contains($itemType)) {
            $this->itemTypes->add($itemType);
            $itemType->setItemNature($this);
        }

        return $this;
    }

    public function removeItemType(ItemType $itemType): self
    {
        if ($this->itemTypes->removeElement($itemType)) {
            // set the owning side to null (unless already changed)
            if ($itemType->getItemNature() === $this) {
                $itemType->setItemNature(null);
            }
        }

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
}
