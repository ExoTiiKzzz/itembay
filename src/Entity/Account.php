<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerClass $class = null;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: Item::class)]
    private Collection $inventory;

    public function __construct()
    {
        $this->inventory = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function __toString(){
        return $this->name;
    }

    public function getClass(): ?PlayerClass
    {
        return $this->class;
    }

    public function setClass(?PlayerClass $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getInventory(): Collection
    {
        return $this->inventory;
    }

    public function addItem(Item $item): self
    {
        if (!$this->inventory->contains($item)) {
            $this->inventory->add($item);
            $item->setAccount($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->inventory->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getAccount() === $this) {
                $item->setAccount(null);
            }
        }

        return $this;
    }
}
