<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Stage;
use App\Repository\StageRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

define('WEIGHT_CHANGE', '1');
define('WEIGHT_MOVE_UP', 'up');
define('WEIGHT_MOVE_DOWN', 'down');

define('STAGE_CHANGE', '2');

/**
 * @Rest\Route("/api/v1")
 */
class CardController extends FOSRestController
{
    /**
     * @Rest\Post(path="/cards/add")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $title = $request->request->get('title', '');
            $content = $request->request->get('content', '');
            $idStage = $request->request->get('idColumn', '');

            $card = new Card();
            $card->setTitle($title);
            $card->setContent($content);

            $stage = $entityManager->getRepository(Stage::class)->find($idStage);
            $card->setStage($stage);

            $weight = $stage->getCardsMaxWeight() + 1;
            $card->setWeight($weight);

            $entityManager->persist($card);
            $entityManager->flush();

            $response = [
                'id' => $card->getId(),
            ];

            $jsonResponse = new JsonResponse($response);
        } catch (\Exception $e) {
            $response = [
                'error' => $e->getMessage(),
            ];

            $jsonResponse = new JsonResponse($response);
            $jsonResponse->setStatusCode('400');
        }
        $jsonResponse->headers->set('Access-Control-Allow-Origin', '*');

        return $jsonResponse;
    }

    /**
     * @Rest\Post(
     *     path="rest/api/cards/update"
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $id = $request->request->get('idTicket');
            $title = $request->request->get('title');
            $content = $request->request->get('content');
            $idStage = $request->request->get('idColumn');

            $card = $entityManager->getRepository(Card::class)->findOneBy(['id' => $id]);

            if (!empty($card)) {
                if (!is_null($title)) {
                    $card->setTitle($title);
                }

                if (!is_null($content)) {
                    $card->setContent($content);
                }

                if (!is_null($idStage)) {
                    $stage = $entityManager->getRepository(Stage::class)->find($idStage);
                    $card->setStage($stage);
                }

                $entityManager->persist($card);
                $entityManager->flush();

                $response = 'success!';
            } else {
                $response = 'not found!';
            }

            $jsonResponse = new JsonResponse($response);
        } catch (\Exception $e) {
            $response = [
                'error' => $e->getMessage(),
            ];

            $jsonResponse = new JsonResponse($response);
            $jsonResponse->setStatusCode('400');
        }
        $jsonResponse->headers->set('Access-Control-Allow-Origin', '*');

        return $jsonResponse;
    }

    /**
     * @Rest\Post(
     *     path="rest/api/cards/update/position"
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updatePositionAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $id = $request->request->get('idTicket');
            $actionType = $request->request->get('actionType');
            $action = $request->request->get('action');
            $idStage = $request->request->get('idColumn');

            $card = $entityManager->getRepository(Card::class)->findOneBy(['id' => $id]);
            $stage = $entityManager->getRepository(Stage::class)->find($idStage);

            if (!empty($card)) {
                switch ($actionType) {
                    case WEIGHT_CHANGE:
                        $stageCards = $stage->getCards();

                        $firstCard = $card;
                        $firstWeight = $card->getWeight();

                        $secondCard = null;
                        $secondWeight = null;

                        $weights = [];
                        foreach ($stageCards as $stageCard) {
                            $weights[$stageCard->getWeight()] = [
                                'id' => $stageCard->getId(),
                                'weight' => $stageCard->getWeight(),
                            ];
                        }
                        ksort($weights);
                        $weights = array_values($weights);

                        foreach ($weights as $index => $weight) {
                            if ($weight['weight'] === (int) $firstWeight) {
                                $counter = $index;
                                while (isset($weights[$counter])) {
                                    if ($action === WEIGHT_MOVE_UP) {
                                        if ($weights[$counter]['weight'] < $firstWeight) {
                                            $secondWeight = $weights[$counter]['weight'];
                                            $secondCard = $entityManager->getRepository(Card::class)->find($weights[$counter]['id']);
                                            break 2;
                                        } else {
                                            --$counter;
                                        }
                                    } else {
                                        if ($weights[$counter]['weight'] > $firstWeight) {
                                            $secondWeight = $weights[$counter]['weight'];
                                            $secondCard = $entityManager->getRepository(Card::class)->find($weights[$counter]['id']);
                                            break 2;
                                        } else {
                                            ++$counter;
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($firstCard) && !empty($secondCard)) {
                            $firstCard->setWeight($secondWeight);
                            $secondCard->setWeight($firstWeight);

                            $entityManager->persist($firstCard);
                            $entityManager->persist($secondCard);
                        } else {
                            throw new \Exception(json_encode($weights));
                        }

                        break;

                    case STAGE_CHANGE:
                        $newStageId = $action;

                        $newStage = $entityManager->getRepository(Stage::class)->find($newStageId);

                        $stageCards = $newStage->getCards();

                        foreach ($stageCards as $stageCard) {
                            $weight = $stageCard->getWeight();
                            $stageCard->setWeight($weight + 1);

                            $entityManager->persist($stageCard);
                        }

                        $card->setStage($newStage);
                        $card->setWeight(0);

                        $entityManager->persist($card);
                        break;
                }

                $entityManager->flush();

                $response = 'success!';
            } else {
                $response = 'not found!';
            }

            $jsonResponse = new JsonResponse($response);
        } catch (\Exception $e) {
            $response = [
                'error' => $e->getMessage(),
            ];

            $jsonResponse = new JsonResponse($response);
            $jsonResponse->setStatusCode('400');
        }
        $jsonResponse->headers->set('Access-Control-Allow-Origin', '*');

        return $jsonResponse;
    }

    /**
     * @Rest\Post(
     *     path="rest/api/cards/delete"
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $id = $request->request->get('idTicket');

            $card = $entityManager->getRepository(Card::class)->findOneBy(['id' => $id]);

            if (!empty($card)) {
                $entityManager->remove($card);
                $entityManager->flush();

                $response = 'success!';
            } else {
                $response = 'not found!';
            }

            $jsonResponse = new JsonResponse($response);
        } catch (\Exception $e) {
            $response = [
                'error' => $e->getMessage(),
            ];

            $jsonResponse = new JsonResponse($response);
            $jsonResponse->setStatusCode('400');
        }
        $jsonResponse->headers->set('Access-Control-Allow-Origin', '*');

        return $jsonResponse;
    }

    /**
     * @Rest\Get(path="/stages/{stageId}/cards")
     * @Rest\View()
     *
     * @param Request $request
     * @param StageRepository $stageRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getColumnAction(
        Request $request,
        StageRepository $stageRepository
    ) {
        $stage = $stageRepository->findOneBy(['id' => $request->get('stageId')]);
        if (empty($stage)) {
            throw new NotFoundHttpException('Stage not found');
        }

        $cards = $stage->getCards();
        if (empty($cards)) {
            throw new NotFoundHttpException('Cards not found');
        }

        usort($cards, function ($a, $b) {
            return $a->getWeight() - $b->getWeight();
        });

        $view = $this->view($cards, 200);

        return $this->handleView($view);
    }
}
