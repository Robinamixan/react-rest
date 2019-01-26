<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Stage;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

define('WEIGHT_CHANGE', '1');
define('WEIGHT_MOVE_UP', 'up');
define('WEIGHT_MOVE_DOWN', 'down');

define('STAGE_CHANGE', '2');


class RestContentController extends FOSRestController
{
    /**
     * @Rest\Post(
     *     path="rest/api/cards/add"
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function AddAction(Request $request) {
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

            $response = array(
                'id' => $card->getId(),
            );

            $jsonResponse = new JsonResponse($response);
        } catch (\Exception $e) {
            $response = array(
                'error' => $e->getMessage(),
            );

            $jsonResponse = new JsonResponse($response);
            $jsonResponse->setStatusCode('400');
        }

        return $jsonResponse;
    }

    /**
     * @Rest\Post(
     *     path="rest/api/cards/update"
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function UpdateAction(Request $request) {
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $id = $request->request->get('idTicket');
            $title = $request->request->get('title');
            $content = $request->request->get('content');
            $idStage = $request->request->get('idColumn');

            $card = $entityManager->getRepository(Card::class)->findOneBy(array('id' => $id));

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
            $response = array(
                'error' => $e->getMessage(),
            );

            $jsonResponse = new JsonResponse($response);
            $jsonResponse->setStatusCode('400');
        }

        return $jsonResponse;
    }

    /**
     * @Rest\Post(
     *     path="rest/api/cards/update/position"
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function UpdatePositionAction(Request $request) {
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $id = $request->request->get('idTicket');
            $actionType = $request->request->get('actionType');
            $action = $request->request->get('action');
            $idStage = $request->request->get('idColumn');

            $card = $entityManager->getRepository(Card::class)->findOneBy(array('id' => $id));
            $stage = $entityManager->getRepository(Stage::class)->find($idStage);

            if (!empty($card)) {
                switch ($actionType) {
                    case WEIGHT_CHANGE: {
                        $stageCards = $stage->getCards();

                        $firstCard = $card;
                        $firstWeight = $card->getWeight();

                        $secondCard = null;
                        $secondWeight = null;

                        $weights = array();
                        foreach ($stageCards as $stageCard) {
                            $weights[$stageCard->getWeight()] = array(
                                'id' => $stageCard->getId(),
                                'weight' => $stageCard->getWeight(),
                            );
                        }
                        ksort($weights);
                        $weights = array_values($weights);

                        foreach ($weights as $index => $weight) {
                            if ($weight['weight'] === (int)$firstWeight) {
                                $counter = $index;
                                while (isset($weights[$counter])) {
                                    if ($action === WEIGHT_MOVE_UP) {
                                        if ($weights[$counter]['weight'] < $firstWeight) {
                                            $secondWeight = $weights[$counter]['weight'];
                                            $secondCard = $entityManager->getRepository(Card::class)->find($weights[$counter]['id']);
                                            break(2);
                                        } else {
                                            $counter--;
                                        }
                                    } else {
                                        if ($weights[$counter]['weight'] > $firstWeight) {
                                            $secondWeight = $weights[$counter]['weight'];
                                            $secondCard = $entityManager->getRepository(Card::class)->find($weights[$counter]['id']);
                                            break(2);
                                        } else {
                                            $counter++;
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
                    }
                    case STAGE_CHANGE: {
                        $newStageId = $action;

                        $newStage = $entityManager->getRepository(Stage::class)->find($newStageId);

                        $card->setStage($newStage);
                        $card->setWeight(0);
                        $entityManager->persist($card);
                        break;
                    }
                }

                $entityManager->flush();

                $response = 'success!';
            } else {
                $response = 'not found!';
            }

            $jsonResponse = new JsonResponse($response);
        } catch (\Exception $e) {
            $response = array(
                'error' => $e->getMessage(),
            );

            $jsonResponse = new JsonResponse($response);
            $jsonResponse->setStatusCode('400');
        }

        return $jsonResponse;
    }

    /**
     * @Rest\Post(
     *     path="rest/api/cards/delete"
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function DeleteAction(Request $request) {
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $id = $request->request->get('idTicket');

            $card = $entityManager->getRepository(Card::class)->findOneBy(array('id' => $id));

            if (!empty($card)) {
                $entityManager->remove($card);
                $entityManager->flush();

                $response = 'success!';
            } else {
                $response = 'not found!';
            }

            $jsonResponse = new JsonResponse($response);
        } catch (\Exception $e) {
            $response = array(
                'error' => $e->getMessage(),
            );

            $jsonResponse = new JsonResponse($response);
            $jsonResponse->setStatusCode('400');
        }

        return $jsonResponse;
    }

    /**
     * @Rest\Get(
     *     path="rest/api/cards"
     * )
     */
    public function getAction() {
        $entityManager = $this->getDoctrine()->getManager();

        $cards = $entityManager->getRepository(Card::class)->findAll();
        $result = $cards;

        return new JsonResponse($result);
    }

    /**
     * @Rest\Get(
     *     path="rest/api/column/{id_column}/cards"
     * )
     *
     * @param $id_column
     * @return JsonResponse
     */
    public function getColumnAction($id_column) {
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $stage = $entityManager->getRepository(Stage::class)->find($id_column);
            if (empty($stage)) {
                throw new \Exception('there is no stage!');
            }

            $cards = $stage->getCards();

            if (empty($cards)) {
                throw new \Exception('there is no cards!');
            }

            $response = array();
            foreach ($cards as $card) {
                $weight = $card->getWeight();
                $response[$weight] = array(
                    'id' => $card->getId(),
                    'title' => $card->getTitle(),
                    'content' => $card->getContent(),
                    'weight' => $card->getWeight(),
                );
            }

            ksort($response);
            $response = array_values($response);

            $jsonResponse = new JsonResponse($response);
        } catch (\Exception $e) {
            $response = array(
                'error' => $e->getMessage(),
                'arg' => $id_column
            );

            $jsonResponse = new JsonResponse($response);
            $jsonResponse->setStatusCode('400');
        }

        return $jsonResponse;
    }
}