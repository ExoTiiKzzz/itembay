<?php

namespace App\Entity;

use App\Repository\DefaultItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DefaultItemRepository::class)]
#[ORM\HasLifecycleCallbacks]
class DefaultItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;


    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private ?Uuid $uuid = null;

    #[ORM\Column]
    private ?int $buy_price = null;

    #[ORM\Column]
    private ?int $sell_price = null;

    #[ORM\Column(length: 255)]
    private ?string $image_url = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'defaultItems')]
    private ?ItemType $itemType = null;

    #[ORM\OneToMany(mappedBy: 'defaultItem', targetEntity: Item::class, orphanRemoval: true)]
    private Collection $items;

    #[Pure] public function __construct()
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

    public function getUuid(): string
    {
        return $this->uuid->toRfc4122();
    }

    #[ORM\PrePersist]
    public function setUuid(): self
    {
        $this->uuid = Uuid::v4();

        return $this;
    }

    public function getBuyPrice(): ?int
    {
        return $this->buy_price;
    }

    public function setBuyPrice(int $buy_price): self
    {
        $this->buy_price = $buy_price;

        return $this;
    }

    public function getSellPrice(): ?int
    {
        return $this->sell_price;
    }

    public function setSellPrice(int $sell_price): self
    {
        $this->sell_price = $sell_price;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    public function setImageUrl(string $image_url): self
    {
        $this->image_url = $image_url;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getItemType(): ?ItemType
    {
        return $this->itemType;
    }

    public function setItemType(?ItemType $itemType): self
    {
        $this->itemType = $itemType;

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
            $item->setDefaultItem($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getDefaultItem() === $this) {
                $item->setDefaultItem(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
