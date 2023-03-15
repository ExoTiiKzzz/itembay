<?php

namespace App\Entity;

use App\Repository\PlayerClassRepository;
use App\Service\ApiImageService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: PlayerClassRepository::class)]
class PlayerClass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $ankamaId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: ItemType::class, inversedBy: 'playerClasses')]
    private Collection $canBuy;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'class', targetEntity: Account::class)]
    private Collection $accounts;

    #[Pure] public function __construct()
    {
        $this->canBuy = new ArrayCollection();
        $this->accounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnkamaId(): ?int
    {
        return $this->ankamaId;
    }

    public function setAnkamaId(int $id): self
    {
        $this->ankamaId = $id;

        return $this;
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

    /**
     * @return Collection<int, ItemType>
     */
    public function getCanBuy(): Collection
    {
        return $this->canBuy;
    }

    public function addCanBuy(ItemType $canBuy): self
    {
        if (!$this->canBuy->contains($canBuy)) {
            $this->canBuy->add($canBuy);
        }

        return $this;
    }

    public function removeCanBuy(ItemType $canBuy): self
    {
        $this->canBuy->removeElement($canBuy);

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return ApiImageService::$baseApiUrl . 'classes/' . $this->ankamaId . '.png';
    }

    /**
     * @return Collection<int, Account>
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function addAccount(Account $account): self
    {
        if (!$this->accounts->contains($account)) {
            $this->accounts->add($account);
            $account->setClass($this);
        }

        return $this;
    }

    public function removeAccount(Account $account): self
    {
        if ($this->accounts->removeElement($account)) {
            // set the owning side to null (unless already changed)
            if ($account->getClass() === $this) {
                $account->setClass(null);
            }
        }

        return $this;
    }
}
