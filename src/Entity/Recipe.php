<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: RecipeLine::class)]
    private Collection $recipeLines;

    #[ORM\OneToOne(inversedBy: 'recipe', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?DefaultItem $item = null;

    public function __construct()
    {
        $this->recipeLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, RecipeLine>
     */
    public function getRecipeLines(): Collection
    {
        return $this->recipeLines;
    }

    public function addRecipeLine(RecipeLine $recipeLine): self
    {
        if (!$this->recipeLines->contains($recipeLine)) {
            $this->recipeLines->add($recipeLine);
            $recipeLine->setRecipe($this);
        }

        return $this;
    }

    public function removeRecipeLine(RecipeLine $recipeLine): self
    {
        if ($this->recipeLines->removeElement($recipeLine)) {
            // set the owning side to null (unless already changed)
            if ($recipeLine->getRecipe() === $this) {
                $recipeLine->setRecipe(null);
            }
        }

        return $this;
    }

    public function getItem(): ?DefaultItem
    {
        return $this->item;
    }

    public function setItem(DefaultItem $item): self
    {
        $this->item = $item;

        return $this;
    }
}
