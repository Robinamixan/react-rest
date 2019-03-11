<?php

namespace App\Handler;


use App\DTO\CardRequestDto;
use App\Entity\Card;
use App\Repository\CardRepository;
use App\Repository\StageRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateCardRequestHandler
{
    /**
     * @var CardRepository
     */
    private $cardRepository;

    /**
     * UpdateCardRequestHandler constructor.
     *
     * @param StageRepository $stageRepository
     * @param CardRepository $cardRepository
     */
    public function __construct(
        StageRepository $stageRepository,
        CardRepository $cardRepository
    ) {
        $this->cardRepository = $cardRepository;
    }

    /**
     * @param CardRequestDto $dto
     *
     * @return Card
     */
    public function handle(CardRequestDto $dto)
    {
        $stage = $dto->getStage();
        if (empty($stage)) {
            throw new NotFoundHttpException('Stage not found');
        }

        $card = $dto->getCard();
        if (empty($card)) {
            throw new NotFoundHttpException('Card not found');
        }

        return $this->cardRepository->update($card, $dto->getTitle(), $dto->getContent(), $stage);
    }
}