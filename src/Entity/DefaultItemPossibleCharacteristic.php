<?php

namespace App\Entity;

use App\Repository\DefaultItemPossibleCharacteristicRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DefaultItemPossibleCharacteristicRepository::class)]
class DefaultItemPossibleCharacteristic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'possibleCharacteristics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DefaultItem $defaultItem = null;

    #[ORM\ManyToOne(inversedBy: 'defaultItemCharacteristics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Characteristic $characteristic = null;

    #[ORM\Column]
    private ?int $min = null;

    #[ORM\Column]
    private ?int $max = null;

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

    public function getCharacteristic(): ?Characteristic
    {
        return $this->characteristic;
    }

    public function setCharacteristic(?Characteristic $characteristic): self
    {
        $this->characteristic = $characteristic;

        return $this;
    }

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function setMin(int $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(int $max): self
    {
        $this->max = $max;

        return $this;
    }
}
