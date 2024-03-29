<?php

namespace App\Entity;

use App\Repository\LootBoxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LootBoxRepository::class)]
class LootBox
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 6)]
    private ?string $color = null;

    #[ORM\OneToMany(mappedBy: 'lootBox', targetEntity: LootBoxLine::class)]
    private Collection $lootBoxLines;

    #[ORM\Column]
    private ?int $maxFreePerDay = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'lootBox', targetEntity: LootBoxOpening::class)]
    private Collection $lootBoxOpenings;

    public function __construct()
    {
        $this->lootBoxLines = new ArrayCollection();
        $this->lootBoxOpenings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection<int, LootBoxLine>
     */
    public function getLootBoxLines(): Collection
    {
        return $this->lootBoxLines;
    }

    public function addLootBoxLine(LootBoxLine $lootBoxLine): self
    {
        if (!$this->lootBoxLines->contains($lootBoxLine)) {
            $this->lootBoxLines->add($lootBoxLine);
            $lootBoxLine->setLootBox($this);
        }

        return $this;
    }

    public function removeLootBoxLine(LootBoxLine $lootBoxLine): self
    {
        if ($this->lootBoxLines->removeElement($lootBoxLine)) {
            // set the owning side to null (unless already changed)
            if ($lootBoxLine->getLootBox() === $this) {
                $lootBoxLine->setLootBox(null);
            }
        }

        return $this;
    }

    public function getMaxFreePerDay(): ?int
    {
        return $this->maxFreePerDay;
    }

    public function setMaxFreePerDay(int $maxFreePerDay): self
    {
        $this->maxFreePerDay = $maxFreePerDay;

        return $this;
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

    /**
     * @return Collection<int, LootBoxOpening>
     */
    public function getLootBoxOpenings(): Collection
    {
        return $this->lootBoxOpenings;
    }

    public function addLootBoxOpening(LootBoxOpening $lootBoxOpening): self
    {
        if (!$this->lootBoxOpenings->contains($lootBoxOpening)) {
            $this->lootBoxOpenings->add($lootBoxOpening);
            $lootBoxOpening->setLootBox($this);
        }

        return $this;
    }

    public function removeLootBoxOpening(LootBoxOpening $lootBoxOpening): self
    {
        if ($this->lootBoxOpenings->removeElement($lootBoxOpening)) {
            // set the owning side to null (unless already changed)
            if ($lootBoxOpening->getLootBox() === $this) {
                $lootBoxOpening->setLootBox(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
