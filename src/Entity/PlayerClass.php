<?php

namespace App\Entity;

use App\Repository\PlayerClassRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerClassRepository::class)]
class PlayerClass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: ItemType::class, inversedBy: 'playerClasses')]
    private Collection $canBuy;

    public function __construct()
    {
        $this->canBuy = new ArrayCollection();
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
     * @return Collection<int, ItemType>
     */
    public function getCanBuy(): Collection
    {
        return $this->canBuy;
    }

    public function addCanBuy(ItemType $canBuy): self
    {
        if (!$this->canBuy->contains($canBuy)) {
            $this->canBuy->add($canBuy);
        }

        return $this;
    }

    public function removeCanBuy(ItemType $canBuy): self
    {
        $this->canBuy->removeElement($canBuy);

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
