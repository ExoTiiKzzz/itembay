<?php

namespace App\Entity;

use App\Repository\BugReportTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: BugReportTypeRepository::class)]
class BugReportType
{
    const DEFAULT_TYPE = 'Bug';
    const BUG_TYPE = 'Bug';
    const UPGRADE_TYPE = 'Amélioration';
    const FEATURE_TYPE = 'Nouvelle fonctionnalité';
    const OTHER_TYPE = 'Autre';

    const TYPES = [
        self::BUG_TYPE,
        self::UPGRADE_TYPE,
        self::FEATURE_TYPE,
        self::OTHER_TYPE,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: BugReport::class)]
    private Collection $bugReports;

    #[Pure] public function __construct()
    {
        $this->bugReports = new ArrayCollection();
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

    /**
     * @return Collection<int, BugReport>
     */
    public function getBugReports(): Collection
    {
        return $this->bugReports;
    }

    public function addBugReport(BugReport $bugReport): self
    {
        if (!$this->bugReports->contains($bugReport)) {
            $this->bugReports->add($bugReport);
            $bugReport->setType($this);
        }

        return $this;
    }

    public function removeBugReport(BugReport $bugReport): self
    {
        if ($this->bugReports->removeElement($bugReport)) {
            // set the owning side to null (unless already changed)
            if ($bugReport->getType() === $this) {
                $bugReport->setType(null);
            }
        }

        return $this;
    }
}
