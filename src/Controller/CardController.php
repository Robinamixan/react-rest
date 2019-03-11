<?php

namespace App\Controller;

use App\DTO\CardRequestDto;
use App\Entity\Card;
use App\Entity\Stage;
use App\Handler\AddCardRequestHandler;
use App\Handler\DeleteCardRequestHandler;
use App\Handler\UpdateCardPositionRequestHandler;
use App\Handler\UpdateCardRequestHandler;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Rest\Route("/api/v1/stages/{stageId}/cards")
 */
class CardController extends FOSRestController
{
    /**
     * @Rest\Post(path="/add")
     * @Rest\View()
     *
     * @ParamConverter("dto", converter="fos_rest.request_body")
     * @Entity("stage", expr="repository.find(stageId)")
     *
     * @param CardRequestDto $dto
     * @param AddCardRequestHandler $handler
     * @param Stage|null $stage
     *
     * @return Card|\FOS\RestBundle\View\View
     */
    public function addCard(
        CardRequestDto $dto,
        AddCardRequestHandler $handler,
        Stage $stage = null
    ) {
        $dto->setStage($stage);

        return $handler->handle($dto);
    }

    /**
     * @Rest\Post(path="/{cardId}/update")
     * @Rest\View()
     *
     * @ParamConverter("dto", converter="fos_rest.request_body")
     * @Entity("stage", expr="repository.find(stageId)")
     * @Entity("card", expr="repository.find(cardId)")
     *
     * @param CardRequestDto $dto
     * @param UpdateCardRequestHandler $handler
     * @param Stage|null $stage
     * @param Card|null $card
     *
     * @return Card
     */
    public function updateCard(
        CardRequestDto $dto,
        UpdateCardRequestHandler $handler,
        Stage $stage = null,
        Card $card = null
    ) {
        $dto->setStage($stage);
        $dto->setCard($card);

        return $handler->handle($dto);
    }

    /**
     * @Rest\Post(path="/{cardId}/update/position")
     * @Rest\View()
     *
     * @ParamConverter("dto", converter="fos_rest.request_body")
     * @Entity("stage", expr="repository.find(stageId)")
     * @Entity("card", expr="repository.find(cardId)")
     *
     * @param CardRequestDto $dto
     * @param UpdateCardPositionRequestHandler $handler
     *
     * @param Stage|null $stage
     * @param Card|null $card
     * @return Card
     */
    public function updateCardPosition(
        CardRequestDto $dto,
        UpdateCardPositionRequestHandler $handler,
        Stage $stage = null,
        Card $card = null
    ) {
        $dto->setStage($stage);
        $dto->setCard($card);

        return $handler->handle($dto);
    }

    /**
     * @Rest\Post(path="/{cardId}/delete")
     * @Rest\View()
     *
     * @ParamConverter("dto", converter="fos_rest.request_body")
     * @Entity("card", expr="repository.find(cardId)")
     *
     * @param CardRequestDto $dto
     * @param DeleteCardRequestHandler $handler
     * @param Card|null $card
     *
     * @return array
     */
    public function deleteCard(
        CardRequestDto $dto,
        DeleteCardRequestHandler $handler,
        Card $card = null
    ) {
        $dto->setCard($card);

        return $handler->handle($dto);
    }

    /**
     * @Rest\Get(path="/")
     * @Rest\View()
     *
     * @Entity("stage", expr="repository.find(stageId)")
     *
     * @param Stage|null $stage
     *
     * @return Card[]|array
     */
    public function getCards(
        Stage $stage = null
    ) {
        $cards = $stage->getCards();
        if (empty($cards)) {
            throw new NotFoundHttpException('Cards not found');
        }

        return $cards;
    }
}
