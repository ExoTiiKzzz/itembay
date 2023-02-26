<?php

namespace App\Entity;

use App\Repository\PlayerClassRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }
}
