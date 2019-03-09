<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\Stage;
use App\Repository\StageRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function AddAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $title = $request->request->get('title', '');
            $boardId = $request->request->get('idBoard', '');

            $stage = new Stage();
            $stage->setTitle($title);

            $board = $entityManager->getRepository(Board::class)->find($boardId);
            $stage->setBoard($board);

            $entityManager->persist($stage);
            $entityManager->flush();

            $response = 'success!';

            $jsonResponse = new JsonResponse($response);
        } catch (\Exception $e) {
            $response = [
                'error' => $e->getMessage(),
                'arg' => '',
            ];

            $jsonResponse = new JsonResponse($response);
            $jsonResponse->setStatusCode('200');
        }
        $jsonResponse->headers->set('Access-Control-Allow-Origin', '*');

        return $jsonResponse;
    }

    /**
     * @Rest\Get(path="/stages")
     * @Rest\View()
     *
     * @param StageRepository $stageRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction(
        StageRepository $stageRepository
    ) {
        $stages = $stageRepository->findAll();

        if (!$stages) {
            throw new NotFoundHttpException('Stages not found');
        }

        $view = $this->view($stages, 200);

        return $this->handleView($view);
    }
}
