<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Column;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Routing\ClassResourceInterface;

class RestColumnController extends FOSRestController
{
    /**
     * @Rest\Post(
     *     path="rest/api/columns/add"
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function postAddAction(Request $request) {
        $entityManager = $this->getDoctrine()->getManager();

        $title = $request->request->get('title', '');
        $board = $request->request->get('board_id', '');

        $card = new Column();
        $card->setTitle($title);
        $card->setTitle();

        $entityManager->persist($card);
        $entityManager->flush();

        $response = 'success!';

        return new JsonResponse($response);
    }

    /**
     * @Rest\Post(
     *     path="rest/api/columns/update"
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function postUpdateAction(Request $request) {
        $entityManager = $this->getDoctrine()->getManager();

        $id = $request->request->get('idTicket', '');
        $title = $request->request->get('title', '');
        $content = $request->request->get('content', '');
        $id_column = $request->request->get('idColumn', '');

        $card = $entityManager->getRepository(Card::class)->findOneBy(array('id' => $id));

        if (!empty($card)) {
            $card->setTitle($title);
            $card->setContent($content);
            $card->setIdColumn($id_column);

            $entityManager->persist($card);
            $entityManager->flush();

            $response = 'success!';
        } else {
            $response = 'not found!';
        }


        return new JsonResponse($response);
    }

    /**
     * @Rest\Get(
     *     path="rest/api/columns"
     * )
     */
    public function getAction() {
        $entityManager = $this->getDoctrine()->getManager();

        $cards = $entityManager->getRepository(Card::class)->findAll();
        $result = $cards;

        return new JsonResponse($result);
    }
}