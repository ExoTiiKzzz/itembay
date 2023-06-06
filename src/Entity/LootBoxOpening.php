<?php

namespace App\Entity;

use App\Repository\LootBoxOpeningRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LootBoxOpeningRepository::class)]
class LootBoxOpening
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lootBoxOpenings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ORM\ManyToOne(inversedBy: 'lootBoxOpenings')]
    private ?LootBox $lootBox = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
