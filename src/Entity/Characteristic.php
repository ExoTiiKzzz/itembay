<?php

namespace App\Entity;

use App\Repository\CharacteristicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CharacteristicRepository::class)]
class Characteristic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $showOrder = null;

    #[ORM\Column]
    private ?int $ankamaId = null;

    #[ORM\OneToMany(mappedBy: 'characteristic', targetEntity: DefaultItemPossibleCharacteristic::class, orphanRemoval: true)]
    private Collection $defaultItemCharacteristics;

    #[ORM\OneToMany(mappedBy: 'characteristic', targetEntity: ItemCurrentCharacteristic::class)]
    private Collection $defaultItemCurrentCharacteristics;

    public function __construct()
    {
        $this->defaultItemCharacteristics = new ArrayCollection();
        $this->defaultItemCurrentCharacteristics = new ArrayCollection();
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

    public function getShowOrder(): ?int
    {
        return $this->showOrder;
    }

    public function setShowOrder(int $showOrder): self
    {
        $this->showOrder = $showOrder;

        return $this;
    }

    public function getAnkamaId(): ?int
    {
        return $this->ankamaId;
    }

    public function setAnkamaId(int $ankamaId): self
    {
        $this->ankamaId = $ankamaId;

        return $this;
    }

    /**
     * @return Collection<int, DefaultItemPossibleCharacteristic>
     */
    public function getDefaultItemCharacteristics(): Collection
    {
        return $this->defaultItemCharacteristics;
    }

    public function addDefaultItemCharacteristic(DefaultItemPossibleCharacteristic $defaultItemCharacteristic): self
    {
        if (!$this->defaultItemCharacteristics->contains($defaultItemCharacteristic)) {
            $this->defaultItemCharacteristics->add($defaultItemCharacteristic);
            $defaultItemCharacteristic->setCharacteristic($this);
        }

        return $this;
    }

    public function removeDefaultItemCharacteristic(DefaultItemPossibleCharacteristic $defaultItemCharacteristic): self
    {
        if ($this->defaultItemCharacteristics->removeElement($defaultItemCharacteristic)) {
            // set the owning side to null (unless already changed)
            if ($defaultItemCharacteristic->getCharacteristic() === $this) {
                $defaultItemCharacteristic->setCharacteristic(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ItemCurrentCharacteristic>
     */
    public function getDefaultItemCurrentCharacteristics(): Collection
    {
        return $this->defaultItemCurrentCharacteristics;
    }

    public function addDefaultItemCurrentCharacteristic(ItemCurrentCharacteristic $defaultItemCurrentCharacteristic): self
    {
        if (!$this->defaultItemCurrentCharacteristics->contains($defaultItemCurrentCharacteristic)) {
            $this->defaultItemCurrentCharacteristics->add($defaultItemCurrentCharacteristic);
            $defaultItemCurrentCharacteristic->setCharacteristic($this);
        }

        return $this;
    }

    public function removeDefaultItemCurrentCharacteristic(ItemCurrentCharacteristic $defaultItemCurrentCharacteristic): self
    {
        if ($this->defaultItemCurrentCharacteristics->removeElement($defaultItemCurrentCharacteristic)) {
            // set the owning side to null (unless already changed)
            if ($defaultItemCurrentCharacteristic->getCharacteristic() === $this) {
                $defaultItemCurrentCharacteristic->setCharacteristic(null);
            }
        }

        return $this;
    }
}
