<?php

namespace App\Controller;

use App\Entity\Card;
use App\Handler\AddCardRequestHandler;
use App\Handler\DeleteCardRequestHandler;
use App\Handler\GetCardsQueryHandler;
use App\Handler\UpdateCardPositionRequestHandler;
use App\Handler\UpdateCardRequestHandler;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Rest\Route("/api/v1/stages/{stageId}/cards")
 */
class CardController extends FOSRestController
{
    /**
     * @Rest\Post(path="/add")
     * @Rest\View()
     *
     * @param Request $request
     * @param AddCardRequestHandler $handler
     *
     * @return \App\Entity\Card
     */
    public function addCard(
        Request $request,
        AddCardRequestHandler $handler
    ) {
        return $handler->handle($request);
    }

    /**
     * @Rest\Post(path="/{cardId}/update")
     * @Rest\View()
     *
     * @param Request $request
     *
     * @param UpdateCardRequestHandler $handler
     * @return Card
     */
    public function updateCard(
        Request $request,
        UpdateCardRequestHandler $handler
    ) {
        return $handler->handle($request);
    }

    /**
     * @Rest\Post(path="/{cardId}/update/position")
     * @Rest\View()
     *
     * @param Request $request
     * @param UpdateCardPositionRequestHandler $handler
     *
     * @return Card
     */
    public function updateCardPosition(
        Request $request,
        UpdateCardPositionRequestHandler $handler
    ) {
        return $handler->handle($request);
    }

    /**
     * @Rest\Post(path="/delete")
     * @Rest\View()
     *
     * @param Request $request
     *
     * @param DeleteCardRequestHandler $handler
     * @return array
     */
    public function deleteCard(
        Request $request,
        DeleteCardRequestHandler $handler
    ) {
        return $handler->handle($request);
    }

    /**
     * @Rest\Get(path="/")
     * @Rest\View()
     *
     * @param Request $request
     *
     * @param GetCardsQueryHandler $handler
     * @return Card[]|array
     */
    public function getCards(
        Request $request,
        GetCardsQueryHandler $handler
    ) {
        return $handler->handle($request);
    }
}
