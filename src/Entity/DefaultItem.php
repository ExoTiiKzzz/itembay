<?php

namespace App\Entity;

use App\Repository\DefaultItemRepository;
use App\Service\ApiImageService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DefaultItemRepository::class)]
#[ORM\HasLifecycleCallbacks]
class DefaultItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $ankamaId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;


    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private ?Uuid $uuid = null;

    #[ORM\Column]
    private ?int $buy_price = null;

    #[ORM\Column]
    private ?int $sell_price = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'defaultItems')]
    private ?ItemType $itemType = null;

    #[ORM\OneToMany(mappedBy: 'defaultItem', targetEntity: Item::class, orphanRemoval: true)]
    private Collection $items;

    #[ORM\ManyToOne(inversedBy: 'defaultItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ItemNature $itemNature = null;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: Review::class)]
    private Collection $reviews;

    #[ORM\Column]
    private ?int $level = null;

    #[ORM\ManyToOne(inversedBy: 'harvestItems')]
    private ?Profession $profession = null;

    #[ORM\OneToOne(mappedBy: 'item', cascade: ['persist', 'remove'])]
    private ?Recipe $recipe = null;

    #[Pure] public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->reviews = new ArrayCollection();
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

    #[Pure] public function getUuid(): string
    {
        return $this->uuid->toRfc4122();
    }

    #[ORM\PrePersist]
    public function setUuid(): self
    {
        $this->uuid = Uuid::v4();

        return $this;
    }

    public function getBuyPrice(): ?int
    {
        return $this->buy_price;
    }

    public function setBuyPrice(int $buy_price): self
    {
        $this->buy_price = $buy_price;

        return $this;
    }

    public function getSellPrice(): ?int
    {
        return $this->sell_price;
    }

    public function setSellPrice(int $sell_price): self
    {
        $this->sell_price = $sell_price;

        return $this;
    }

    #[Pure] public function getImageUrl(): ?string
    {
        return ApiImageService::$baseApiUrl . $this->getItemNature()->getName() . '/' . $this->ankamaId . '.png';
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getItemType(): ?ItemType
    {
        return $this->itemType;
    }

    public function setItemType(?ItemType $itemType): self
    {
        $this->itemType = $itemType;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setDefaultItem($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getDefaultItem() === $this) {
                $item->setDefaultItem(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getItemNature(): ?ItemNature
    {
        return $this->itemNature;
    }

    public function setItemNature(?ItemNature $itemNature): self
    {
        $this->itemNature = $itemNature;

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
            $review->setItem($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getItem() === $this) {
                $review->setItem(null);
            }
        }

        return $this;
    }

    #[Pure] public function getAverageRating(): float
    {
        if (count($this->reviews) === 0) {
            return 0;
        }
        $sum = 0;
        /** @var Review $review */
        foreach ($this->reviews as $review) {
            $sum += $review->getNote();
        }
        return $sum / count($this->reviews);
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
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

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(Recipe $recipe): self
    {
        // set the owning side of the relation if necessary
        if ($recipe->getItem() !== $this) {
            $recipe->setItem($this);
        }

        $this->recipe = $recipe;

        return $this;
    }
}
