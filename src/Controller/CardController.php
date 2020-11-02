<?php

namespace App\Controller;

use App\DTO\CardRequestDto;
use App\Entity\Card;
use App\Entity\Stage;
use App\Handler\CardRequestHandler;
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
     * @param CardRequestHandler $handler
     * @param Stage|null $stage
     *
     * @return Card|\FOS\RestBundle\View\View
     */
    public function addCard(
        CardRequestDto $dto,
        CardRequestHandler $handler,
        Stage $stage = null
    ) {
        $dto->setStage($stage);

        return $handler->handleAddRequest($dto);
    }

    /**
     * @Rest\Put(path="/{cardId}")
     * @Rest\View()
     *
     * @ParamConverter("dto", converter="fos_rest.request_body")
     * @Entity("stage", expr="repository.find(stageId)")
     * @Entity("card", expr="repository.find(cardId)")
     *
     * @param CardRequestDto $dto
     * @param CardRequestHandler $handler
     * @param Stage|null $stage
     * @param Card|null $card
     *
     * @return Card
     */
    public function updateCard(
        CardRequestDto $dto,
        CardRequestHandler $handler,
        Stage $stage = null,
        Card $card = null
    ) {
        $dto->setStage($stage);
        $dto->setCard($card);

        return $handler->handleUpdateRequest($dto);
    }

    /**
     * @Rest\Put(path="/{cardId}/position")
     * @Rest\View()
     *
     * @ParamConverter("dto", converter="fos_rest.request_body")
     * @Entity("stage", expr="repository.find(stageId)")
     * @Entity("card", expr="repository.find(cardId)")
     *
     * @param CardRequestDto $dto
     * @param CardRequestHandler $handler
     * @param Stage|null $stage
     * @param Card|null $card
     *
     * @return Card
     */
    public function updateCardPosition(
        CardRequestDto $dto,
        CardRequestHandler $handler,
        Stage $stage = null,
        Card $card = null
    ) {
        $dto->setStage($stage);
        $dto->setCard($card);

        return $handler->handleUpdatePositionRequest($dto);
    }

    /**
     * @Rest\Delete(path="/{cardId}")
     * @Rest\View()
     *
     * @ParamConverter("dto", converter="fos_rest.request_body")
     * @Entity("card", expr="repository.find(cardId)")
     *
     * @param CardRequestDto $dto
     * @param CardRequestHandler $handler
     * @param Card|null $card
     */
    public function deleteCard(
        CardRequestDto $dto,
        CardRequestHandler $handler,
        Card $card = null
    ) {
        $dto->setCard($card);

        $handler->handleDeleteRequest($dto);
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
