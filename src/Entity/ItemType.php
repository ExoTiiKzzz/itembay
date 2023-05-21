<?php

namespace App\Entity;

use App\Repository\ItemTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemTypeRepository::class)]
class ItemType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'itemType', targetEntity: DefaultItem::class)]
    private Collection $defaultItems;

    #[ORM\ManyToMany(targetEntity: PlayerClass::class, mappedBy: 'canBuy')]
    private Collection $playerClasses;

    #[ORM\ManyToOne(inversedBy: 'itemTypes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ItemNature $itemNature = null;

    #[ORM\Column]
    private ?int $ankamaId = null;

    public function __construct()
    {
        $this->defaultItems = new ArrayCollection();
        $this->playerClasses = new ArrayCollection();
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

    public function getDefaultItemsCount(): int
    {
        if ($this->defaultItems instanceof Collection) {
            return $this->defaultItems->count();
        }

        return 0;
    }

    public function addDefaultItem(DefaultItem $defaultItem): self
    {
        if (!$this->defaultItems->contains($defaultItem)) {
            $this->defaultItems->add($defaultItem);
            $defaultItem->setItemType($this);
        }

        return $this;
    }

    public function removeDefaultItem(DefaultItem $defaultItem): self
    {
        if ($this->defaultItems->removeElement($defaultItem)) {
            // set the owning side to null (unless already changed)
            if ($defaultItem->getItemType() === $this) {
                $defaultItem->setItemType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PlayerClass>
     */
    public function getPlayerClasses(): Collection
    {
        return $this->playerClasses;
    }

    public function addPlayerClass(PlayerClass $playerClass): self
    {
        if (!$this->playerClasses->contains($playerClass)) {
            $this->playerClasses->add($playerClass);
            $playerClass->addCanBuy($this);
        }

        return $this;
    }

    public function removePlayerClass(PlayerClass $playerClass): self
    {
        if ($this->playerClasses->removeElement($playerClass)) {
            $playerClass->removeCanBuy($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getItemNature(): ?ItemNature
    {
        return $this->itemNature;
    }

    public function setItemNature(?ItemNature $ItemNature): self
    {
        $this->itemNature = $ItemNature;

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
