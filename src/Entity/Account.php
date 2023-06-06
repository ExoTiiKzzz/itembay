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

    #[ORM\Column(length: 255, unique: true)]
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

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: Batch::class)]
    private Collection $batches;

    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: Transaction::class)]
    private Collection $sells;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'friends')]
    private Collection $friends;

    #[ORM\OneToMany(mappedBy: 'fromAccount', targetEntity: PrivateMessage::class)]
    private Collection $privateMessages;

    #[ORM\ManyToMany(targetEntity: Discussion::class, mappedBy: 'accounts')]
    private Collection $discussions;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: LootBoxOpening::class)]
    private Collection $lootBoxOpenings;

    public function __construct()
    {
        $this->inventory = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->playerProfessions = new ArrayCollection();
        $this->batches = new ArrayCollection();
        $this->sells = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->privateMessages = new ArrayCollection();
        $this->discussions = new ArrayCollection();
        $this->lootBoxOpenings = new ArrayCollection();
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

    /**
     * @return Collection<int, Batch>
     */
    public function getBatches(): Collection
    {
        return $this->batches;
    }

    public function addBatch(Batch $batch): self
    {
        if (!$this->batches->contains($batch)) {
            $this->batches->add($batch);
            $batch->setAccount($this);
        }

        return $this;
    }

    public function removeBatch(Batch $batch): self
    {
        if ($this->batches->removeElement($batch)) {
            // set the owning side to null (unless already changed)
            if ($batch->getAccount() === $this) {
                $batch->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getSells(): Collection
    {
        return $this->sells;
    }

    public function addSell(Transaction $sell): self
    {
        if (!$this->sells->contains($sell)) {
            $this->sells->add($sell);
            $sell->setSeller($this);
        }

        return $this;
    }

    public function removeSell(Transaction $sell): self
    {
        if ($this->sells->removeElement($sell)) {
            // set the owning side to null (unless already changed)
            if ($sell->getSeller() === $this) {
                $sell->setSeller(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFriends(): Collection
    {
        $friends = new ArrayCollection();
        /** @var self $friend */
        foreach ($this->friends as $friend) {
            if ($friend->getFollowings()->contains($this)) {
                $friends->add($friend);
            }
        }

        return $friends;
    }

    public function getFollowings(): Collection
    {
        return $this->friends;
    }

    public function getFollowers(): Collection
    {
        $followers = new ArrayCollection();
        foreach ($this->friends as $friend) {
            if ($friend->getFriends()->contains($this)) {
                $followers->add($friend);
            }
        }

        return $followers;
    }

    public function addFriend(self $friend): self
    {
        if (!$this->friends->contains($friend)) {
            $this->friends->add($friend);
        }

        return $this;
    }

    public function removeFriend(self $friend): self
    {
        $this->friends->removeElement($friend);

        return $this;
    }

    /**
     * @return Collection<int, PrivateMessage>
     */
    public function getPrivateMessages(): Collection
    {
        return $this->privateMessages;
    }

    public function addPrivateMessage(PrivateMessage $privateMessage): self
    {
        if (!$this->privateMessages->contains($privateMessage)) {
            $this->privateMessages->add($privateMessage);
            $privateMessage->setFromAccount($this);
        }

        return $this;
    }

    public function removePrivateMessage(PrivateMessage $privateMessage): self
    {
        if ($this->privateMessages->removeElement($privateMessage)) {
            // set the owning side to null (unless already changed)
            if ($privateMessage->getFromAccount() === $this) {
                $privateMessage->setFromAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Discussion>
     */
    public function getDiscussions(): Collection
    {
        return $this->discussions;
    }

    public function addDiscussion(Discussion $discussion): self
    {
        if (!$this->discussions->contains($discussion)) {
            $this->discussions->add($discussion);
            $discussion->addAccount($this);
        }

        return $this;
    }

    public function removeDiscussion(Discussion $discussion): self
    {
        if ($this->discussions->removeElement($discussion)) {
            $discussion->removeAccount($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, LootBoxOpening>
     */
    public function getLootBoxOpenings(): Collection
    {
        return $this->lootBoxOpenings;
    }

    public function addLootBoxOpening(LootBoxOpening $lootBoxOpening): self
    {
        if (!$this->lootBoxOpenings->contains($lootBoxOpening)) {
            $this->lootBoxOpenings->add($lootBoxOpening);
            $lootBoxOpening->setAccount($this);
        }

        return $this;
    }

    public function removeLootBoxOpening(LootBoxOpening $lootBoxOpening): self
    {
        if ($this->lootBoxOpenings->removeElement($lootBoxOpening)) {
            // set the owning side to null (unless already changed)
            if ($lootBoxOpening->getAccount() === $this) {
                $lootBoxOpening->setAccount(null);
            }
        }

        return $this;
    }
}
