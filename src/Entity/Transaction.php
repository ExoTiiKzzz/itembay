<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use App\Service\BasketService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'item')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ORM\OneToMany(mappedBy: 'transaction', targetEntity: TransactionLine::class)]
    private Collection $transactionLines;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct()
    {
        $this->transactionLines = new ArrayCollection();
    }

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
            $transactionLine->setTransaction($this);
        }

        return $this;
    }

    public function removeTransactionLine(TransactionLine $transactionLine): self
    {
        if ($this->transactionLines->removeElement($transactionLine)) {
            // set the owning side to null (unless already changed)
            if ($transactionLine->getTransaction() === $this) {
                $transactionLine->setTransaction(null);
            }
        }

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

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->created_at = new \DateTimeImmutable();
    }

    #[Pure] #[ArrayShape(['defaultItems' => "array", 'customItems' => "array", 'totalCount' => "int"])]
    public function getItemsFormatted(): array
    {
        $items = [];
        foreach ($this->getTransactionLines() as $transactionLine) {
            $items[] = $transactionLine->getItem();
        }
        return BasketService::listItems($items);
    }
}
