<?php

namespace App\Entity;

use App\Repository\PlayerProfessionRepository;
use App\Service\ProfessionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerProfessionRepository::class)]
class PlayerProfession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $exp = 0;

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

    public function getExp(): ?int
    {
        return $this->exp;
    }

    public function setExp(int $exp): self
    {
        $this->exp = $exp;

        return $this;
    }

    public function getLevel(EntityManagerInterface $em): ?int
    {
        return ProfessionService::getProfessionLevelFromExp($this->getExp(), $em);
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
