<?php

namespace App\Entity;

use App\Repository\ProfessionRepository;
use App\Service\ApiImageService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: ProfessionRepository::class)]
class Profession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $ankamaId = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'profession', targetEntity: PlayerProfession::class)]
    private Collection $playerProfessions;

    public function __construct()
    {
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

    public function getAnkamaId(): ?int
    {
        return $this->ankamaId;
    }

    public function setAnkamaId(int $ankamaId): self
    {
        $this->ankamaId = $ankamaId;

        return $this;
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
            $playerProfession->setProfession($this);
        }

        return $this;
    }

    public function removePlayerProfession(PlayerProfession $playerProfession): self
    {
        if ($this->playerProfessions->removeElement($playerProfession)) {
            // set the owning side to null (unless already changed)
            if ($playerProfession->getProfession() === $this) {
                $playerProfession->setProfession(null);
            }
        }

        return $this;
    }

    #[Pure(true)] public function getImageUrl(): string
    {
        return ApiImageService::$baseApiUrl . 'metiers/' . $this->getAnkamaId() . '.png';
    }
}