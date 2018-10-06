<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Stage;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


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
            $id = $request->request->get('idTicket', '');
            $title = $request->request->get('title', '');
            $content = $request->request->get('content', '');
            $idStage = $request->request->get('idColumn', '');

            $card = $entityManager->getRepository(Card::class)->findOneBy(array('id' => $id));

            if (!empty($card)) {
                $card->setTitle($title);
                $card->setContent($content);

                $stage = $entityManager->getRepository(Stage::class)->find($idStage);
                $card->setStage($stage);

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
     *     path="rest/api/cards/column/{id_column}"
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
                $response[] = array(
                    'id' => $card->getId(),
                    'content' => $card->getContent()
                );
            }

            $jsonResponse = new JsonResponse($response);
        } catch (\Exception $e) {
            $response = array(
                'error' => $e->getMessage(),
                'arg' => $id_column
            );

            $jsonResponse = new JsonResponse($response);
            $jsonResponse->setStatusCode('200');
        }

        return $jsonResponse;
    }
}