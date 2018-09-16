<?php

namespace App\Controller;

use App\Entity\Card;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Routing\ClassResourceInterface;

/**
* @Rest\RouteResource(
*     "rest/api/cards",
*     pluralize=false
* )
*/
class RestContentController extends FOSRestController implements ClassResourceInterface
{
    public function postAction(Request $request) {
        return new JsonResponse(var_export($request->request->get('title'), true));
    }

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
     *     path="rest/api/cards/get"
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
     *     path="rest/api/cards/get/column/{id_column}"
     * )
     */
    public function getColumnAction($id_column) {
        $entityManager = $this->getDoctrine()->getManager();

        $cards = $entityManager->getRepository(Card::class)->findBy(array('id_column' => $id_column));

        $response = array();
        foreach ($cards as $card) {
            $response[] = array(
                'id' => $card->getId(),
                'content' => $card->getContent()
            );
        }

        return new JsonResponse($response);
    }

//var_export(, true)
}