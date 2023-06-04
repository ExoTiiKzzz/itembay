<?php

namespace App\Entity;

use App\Repository\LootBoxLineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LootBoxLineRepository::class)]
class LootBoxLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lootBoxLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DefaultItem $defaultItem = null;

    #[ORM\Column]
    private ?float $probability = null;

    #[ORM\ManyToOne(inversedBy: 'lootBoxLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LootBox $lootBox = null;

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

    public function getProbability(): ?float
    {
        return $this->probability;
    }

    public function setProbability(float $probability): self
    {
        $this->probability = $probability;

        return $this;
    }

    public function getLootBox(): ?LootBox
    {
        return $this->lootBox;
    }

    public function setLootBox(?LootBox $lootBox): self
    {
        $this->lootBox = $lootBox;

        return $this;
    }
}
