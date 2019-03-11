<?php

namespace App\Handler;


use App\DTO\CardRequestDto;
use App\Entity\Card;
use App\Repository\CardRepository;
use App\Repository\StageRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AddCardRequestHandler
{
    /**
     * @var StageRepository
     */
    private $stageRepository;

    /**
     * @var CardRepository
     */
    private $cardRepository;

    /**
     * AddCardRequestHandler constructor.
     *
     * @param StageRepository $stageRepository
     * @param CardRepository $cardRepository
     */
    public function __construct(
        StageRepository $stageRepository,
        CardRepository $cardRepository
    ) {
        $this->stageRepository = $stageRepository;
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

        $weight = $this->stageRepository->getCardsMaxWeight($stage) + 1;

        return $this->cardRepository->create($dto->getTitle(), $dto->getContent(), $stage, $weight);
    }
}