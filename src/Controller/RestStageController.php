<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\Card;
use App\Entity\Stage;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class RestStageController extends FOSRestController
{
    /**
     * @Rest\Post(
     *     path="rest/api/stages/add"
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function AddAction(Request $request) {
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
            $response = array(
                'error' => $e->getMessage(),
                'arg' => ''
            );

            $jsonResponse = new JsonResponse($response);
            $jsonResponse->setStatusCode('200');
        }
        $jsonResponse->headers->set('Access-Control-Allow-Origin', '*');

        return $jsonResponse;
    }

    /**
     * @Rest\Post(
     *     path="rest/api/stages/update"
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function UpdateAction(Request $request) {
        $entityManager = $this->getDoctrine()->getManager();

//        $id = $request->request->get('idTicket', '');
//        $title = $request->request->get('title', '');
//        $content = $request->request->get('content', '');
//        $id_column = $request->request->get('idColumn', '');
//
//        $card = $entityManager->getRepository(Stage::class)->findOneBy(array('id' => $id));
//
//        if (!empty($card)) {
//            $card->setTitle($title);
//            $card->setContent($content);
//            $card->setIdColumn($id_column);
//
//            $entityManager->persist($card);
//            $entityManager->flush();
//
//            $response = 'success!';
//        } else {
//            $response = 'not found!';
//        }

        $response = 'empty function!';
        $response->headers->set('Access-Control-Allow-Origin', '*');


        return new JsonResponse($response);
    }

    /**
     * @Rest\Get(
     *     path="rest/api/stages"
     * )
     */
    public function getAction() {
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $stages = $entityManager->getRepository(Stage::class)->findAll();
            if (empty($stages)) {
                throw new \Exception('there are no stages!');
            }

            $response = array();
            foreach ($stages as $stage) {
                $response[] = array(
                    'id' => $stage->getId(),
                    'title' => $stage->getTitle(),
                );
            }

            $jsonResponse = new JsonResponse($response);
        } catch (\Exception $e) {
            $response = array(
                'error' => $e->getMessage(),
            );

            $jsonResponse = new JsonResponse($response);
            $jsonResponse->setStatusCode('200');
        }

        $jsonResponse->headers->set('Access-Control-Allow-Origin', '*');

        return $jsonResponse;
    }
}