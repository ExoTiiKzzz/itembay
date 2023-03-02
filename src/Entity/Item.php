<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DefaultItem $defaultItem = null;

    #[ORM\Column]
    private ?int $buyPrice = null;

    #[ORM\Column]
    private ?int $sellPrice = null;

    #[ORM\Column]
    private ?bool $isDefaultItem = null;

    public function getId(): ?int
    {
        return $this->id;
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

    #[Pure] public function __toString(): string
    {
        return $this->defaultItem->getName();
    }

    public function getBuyPrice(): ?int
    {
        return $this->buyPrice;
    }

    public function setBuyPrice(int $buyPrice): self
    {
        $this->buyPrice = $buyPrice;

        return $this;
    }

    public function getSellPrice(): ?int
    {
        return $this->sellPrice;
    }

    public function setSellPrice(int $sellPrice): self
    {
        $this->sellPrice = $sellPrice;

        return $this;
    }

    public function isDefaultItem(): ?bool
    {
        return $this->isDefaultItem;
    }

    public function setIsDefaultItem(bool $isDefaultItem): self
    {
        $this->isDefaultItem = $isDefaultItem;

        return $this;
    }
}
