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

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: Transaction::class)]
    private Collection $transactions;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: Review::class)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'player', targetEntity: PlayerProfession::class)]
    private Collection $playerProfessions;

    public function __construct()
    {
        $this->inventory = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->playerProfessions = new ArrayCollection();
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

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setAccount($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getAccount() === $this) {
                $transaction->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setAccount($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getAccount() === $this) {
                $review->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PlayerProfession>
     */
    public function getPlayerProfessions(): Collection
    {
        return $this->playerProfessions;
    }

    public function addPlayerProfession(PlayerProfession $playerProfession): self
    {
        if (!$this->playerProfessions->contains($playerProfession)) {
            $this->playerProfessions->add($playerProfession);
            $playerProfession->setPlayer($this);
        }

        return $this;
    }

    public function removePlayerProfession(PlayerProfession $playerProfession): self
    {
        if ($this->playerProfessions->removeElement($playerProfession)) {
            // set the owning side to null (unless already changed)
            if ($playerProfession->getPlayer() === $this) {
                $playerProfession->setPlayer(null);
            }
        }

        return $this;
    }
}
