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
}
