<?php

namespace App\Handler;


use App\DTO\CardRequestDto;
use App\Repository\CardRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteCardRequestHandler
{
    /**
     * @var CardRepository
     */
    private $cardRepository;

    /**
     * DeleteCardRequestHandler constructor.
     *
     * @param CardRepository $cardRepository
     */
    public function __construct(
        CardRepository $cardRepository
    ) {
        $this->cardRepository = $cardRepository;
    }

    /**
     * @param CardRequestDto $dto
     *
     * @return array
     */
    public function handle(CardRequestDto $dto): array
    {
        $card = $dto->getCard();
        if (empty($card)) {
            throw new NotFoundHttpException('Card not found');
        }

        $this->cardRepository->remove($card);
        return [];
    }
}