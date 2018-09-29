<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Column;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Routing\ClassResourceInterface;


class RestContentController extends FOSRestController
{
//    public function postAction(Request $request) {
//        return new JsonResponse(var_export($request->request->get('title'), true));
//    }

    /**
     * @Rest\Post(
     *     path="rest/api/cards/add"
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function postAddAction(Request $request) {
        $entityManager = $this->getDoctrine()->getManager();

        $title = $request->request->get('title', '');
        $content = $request->request->get('content', '');
        $id_column = $request->request->get('idColumn', '');

        $card = new Card();
        $card->setTitle($title);
        $card->setContent($content);
        $card->setIdColumn($id_column);

        $entityManager->persist($card);
        $entityManager->flush();

        $response = 'success!';

        return new JsonResponse($response);
    }

    /**
     * @Rest\Post(
     *     path="rest/api/cards/update"
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

        $column = $entityManager->getRepository(Column::class)->find((int) $id_column);

        $cards = array();
//        $cards = $column->getCards();
//        $cards = $entityManager->getRepository(Card::class)->findBy(array('id_column' => $id_column));

        $response = array();
        if (!empty($cards)) {
            foreach ($cards as $card) {
                $response[] = array(
                    'id' => $card->getId(),
                    'content' => $card->getContent()
                );
            }
        }

        return new JsonResponse($response);
    }
}