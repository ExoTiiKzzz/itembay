<?php

namespace App\Entity;

use App\Repository\TradeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TradeRepository::class)]
class Trade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $topic = null;

    #[ORM\ManyToOne(inversedBy: 'trades')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $firstAccount = null;

    #[ORM\ManyToOne(inversedBy: 'trades')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $secondAccount = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTopic(): ?string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getFirstAccount(): ?Account
    {
        return $this->firstAccount;
    }

    public function setFirstAccount(?Account $firstAccount): self
    {
        $this->firstAccount = $firstAccount;

        return $this;
    }

    public function getSecondAccount(): ?Account
    {
        return $this->secondAccount;
    }

    public function setSecondAccount(?Account $secondAccount): self
    {
        $this->secondAccount = $secondAccount;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
