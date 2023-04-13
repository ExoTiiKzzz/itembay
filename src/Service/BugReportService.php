<?php

namespace App\Service;

use App\Entity\BugReport;
use App\Entity\BugReportStatus;
use App\Entity\BugReportType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class BugReportService
{
    public static function save(EntityManagerInterface $em, array $data, User $user): void
    {
        $bugReport = $em->getRepository(BugReport::class)->find($data['id'] ?? null) ?: new BugReport();
        $type = $em->getRepository(BugReportType::class)->find($data['type']);
        $bugReport->setType($type);
        $bugReport->setTitle($data['title']);
        $bugReport->setDescription($data['description']);
        $bugReport->setUser($user);

        if (isset($data['status'])) {
            $status = $em->getRepository(BugReportStatus::class)->find($data['status']);
            $bugReport->setStatus($status);
        } else {
            $bugReport->setStatus($em->getRepository(BugReportStatus::class)->findOneBy(['name' => BugReportStatus::IN_PROGRESS_STATUS]));
        }

        $em->persist($bugReport);
        $em->flush();
    }

}