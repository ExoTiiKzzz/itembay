<?php

namespace App\Entity;

use App\Repository\BugReportStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: BugReportStatusRepository::class)]
class BugReportStatus
{
    const DEFAULT_STATUS = 'En cours';
    const IN_PROGRESS_STATUS = 'En cours';
    const DONE_STATUS = 'Terminé';

    const STATUSES = [
        self::IN_PROGRESS_STATUS,
        self::DONE_STATUS,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'bugReportStatus', targetEntity: BugReport::class)]
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
            $bugReport->setStatus($this);
        }

        return $this;
    }

    public function removeBugReport(BugReport $bugReport): self
    {
        if ($this->bugReports->removeElement($bugReport)) {
            // set the owning side to null (unless already changed)
            if ($bugReport->getStatus() === $this) {
                $bugReport->setStatus(null);
            }
        }

        return $this;
    }

    #[Pure] public function __toString(): string
    {
        return $this->getName();
    }
}
