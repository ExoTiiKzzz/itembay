<?php

namespace App\Controller;

use App\Entity\BugReport;
use App\Entity\BugReportStatus;
use App\Entity\BugReportType;
use App\Repository\BugReportStatusRepository;
use App\Repository\BugReportTypeRepository;
use App\Service\ApiResponseService;
use App\Service\BugReportService;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BugReportController extends BaseController
{
    #[Route('/bug/report', name: 'app_bug_report', methods: ['GET'])]
    public function index(): Response
    {
        $bugReportTypes = $this->em->getRepository(BugReportType::class)->findAll();
        return $this->render('bug_report/index.html.twig', [
            'bugReportTypes'    => $bugReportTypes,
            'report'            => new BugReport(),
        ]);
    }

    #[Route('/bug/report', name: 'app_bug_report_save', methods: ['POST'])]
    public function create(): Response
    {
        $user = $this->getUserOrRedirect();
        $bugReport = $this->em->getRepository(BugReport::class)->find($this->request->get('bug_report')['id'] ?? null);
        if ($bugReport && $bugReport->getUser()->getId() !== $user->getId()) {
            return $this->redirectToRoute('app_bug_report_list');
        }
        $data = $this->request->get('bug_report');
        BugReportService::save($this->em, $data, $user);
        return $this->redirectToRoute('app_bug_report_list');
    }

    #[Route('/bug/report/list', name: 'app_bug_report_list', methods: ['GET'])]
    public function list(): Response
    {
        $reports = $this->em->getRepository(BugReport::class)->findBy([], ['id' => 'DESC']);
        return $this->render('bug_report/list.html.twig', [
            'reports' => $reports,
        ]);
    }

    #[Route('/bug/report/{id}', name: 'app_bug_report_show', methods: ['GET'])]
    public function show(BugReport $bugReport): Response
    {
        $bugReportTypes = $this->em->getRepository(BugReportType::class)->findAll();
        return $this->render('bug_report/index.html.twig', [
            'report'            => $bugReport,
            'bugReportTypes'    => $bugReportTypes,
        ]);
    }

    #[Route('/bug/report/{id}', name: 'app_bug_report_delete', methods: ['DELETE'])]
    public function delete(BugReport $bugReport): Response
    {
        try {
            $user = $this->getUserOrRedirect();
            if ($bugReport->getUser()->getId() !== $user->getId()) {
                return $this->redirectToRoute('app_bug_report_list');
            }

            $this->em->remove($bugReport);
            $this->em->flush();

            $table = $this->getTable();
            return ApiResponseService::success([
                'html' => $table,
            ]);
        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }
    }

    private function getTable(): string
    {
        $user = $this->getUserOrRedirect();
        $bugReports = $user->getBugReports();
        return $this->renderView('bug_report/table.html.twig', [
            'reports' => $bugReports,
        ]);
    }
}
