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

        $title = $request->request->get('title', '');
        $boardId = $request->request->get('board_id', '');

        $stage = new Stage();
        $stage->setTitle($title);

        $board = $entityManager->getRepository(Board::class)->find($boardId);
        $stage->setBoard($board);

        $entityManager->persist($stage);
        $entityManager->flush();

        $response = 'success!';

        return new JsonResponse($response);
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


        return new JsonResponse($response);
    }

    /**
     * @Rest\Get(
     *     path="rest/api/stages"
     * )
     */
    public function getAction() {
        $entityManager = $this->getDoctrine()->getManager();

        $cards = $entityManager->getRepository(Stage::class)->findAll();
        $result = $cards;

        return new JsonResponse($result);
    }
}