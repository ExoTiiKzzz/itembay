<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DefaultItem $defaultItem = null;

    #[ORM\Column]
    private ?int $buyPrice = null;

    #[ORM\Column]
    private ?int $sellPrice = null;

    #[ORM\Column]
    private ?bool $isDefaultItem = null;

    #[ORM\ManyToOne(inversedBy: 'inventory')]
    private ?Account $account = null;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: TransactionLine::class)]
    private Collection $transactionLines;

    public function __construct()
    {
        $this->transactionLines = new ArrayCollection();
    }

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
        $this->setIsDefaultItem(true);
        $this->setBuyPrice($defaultItem->getBuyPrice());
        $this->setSellPrice($defaultItem->getSellPrice());

        return $this;
    }

    #[Pure] public function __toString(): string
    {
        return $this->defaultItem->getName();
    }

    public function getBuyPrice(): ?int
    {
        return $this->buyPrice;
    }

    public function setBuyPrice(int $buyPrice): self
    {
        $this->buyPrice = $buyPrice;

        return $this;
    }

    public function getSellPrice(): ?int
    {
        return $this->sellPrice;
    }

    public function setSellPrice(int $sellPrice): self
    {
        $this->sellPrice = $sellPrice;

        return $this;
    }

    public function isDefaultItem(): ?bool
    {
        return $this->isDefaultItem;
    }

    public function setIsDefaultItem(bool $isDefaultItem): self
    {
        $this->isDefaultItem = $isDefaultItem;

        return $this;
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

    /**
     * @return Collection<int, TransactionLine>
     */
    public function getTransactionLines(): Collection
    {
        return $this->transactionLines;
    }

    public function addTransactionLine(TransactionLine $transactionLine): self
    {
        if (!$this->transactionLines->contains($transactionLine)) {
            $this->transactionLines->add($transactionLine);
            $transactionLine->setItem($this);
        }

        return $this;
    }

    public function removeTransactionLine(TransactionLine $transactionLine): self
    {
        if ($this->transactionLines->removeElement($transactionLine)) {
            // set the owning side to null (unless already changed)
            if ($transactionLine->getItem() === $this) {
                $transactionLine->setItem(null);
            }
        }

        return $this;
    }
}
