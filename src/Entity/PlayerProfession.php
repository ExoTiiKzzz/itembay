<?php

namespace App\Entity;

use App\Repository\PlayerProfessionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerProfessionRepository::class)]
class PlayerProfession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $level = null;

    #[ORM\ManyToOne(inversedBy: 'playerProfessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profession $profession = null;

    #[ORM\ManyToOne(inversedBy: 'playerProfessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $player = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getProfession(): ?Profession
    {
        return $this->profession;
    }

    public function setProfession(?Profession $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    public function getPlayer(): ?Account
    {
        return $this->player;
    }

    public function setPlayer(?Account $player): self
    {
        $this->player = $player;

        return $this;
    }
}
