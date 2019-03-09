<?php

namespace App\Controller;

use App\Entity\Stage;
use App\Handler\AddStageRequestHandler;
use App\Repository\StageRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Rest\Route("/api/v1")
 */
class StageController extends FOSRestController
{
    /**
     * @Rest\Post(path="/stages/add")
     * @Rest\View()
     *
     * @param Request $request
     * @param AddStageRequestHandler $handler
     *
     * @return Stage
     */
    public function addStage(
        Request $request,
        AddStageRequestHandler $handler
    ) {
        return $handler->handle($request);
    }

    /**
     * @Rest\Get(path="/stages")
     * @Rest\View()
     *
     * @param StageRepository $stageRepository
     *
     * @return Stage[]
     */
    public function getStages(
        StageRepository $stageRepository
    ) {
        $stages = $stageRepository->findAll();

        if (!$stages) {
            throw new NotFoundHttpException('Stages not found');
        }

        return $stages;
    }
}
