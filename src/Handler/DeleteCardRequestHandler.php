<?php

namespace App\Handler;


use App\Entity\Card;
use App\Repository\CardRepository;
use Symfony\Component\HttpFoundation\Request;
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

    public function handle(Request $request)
    {
        $id = $request->get('cardId');

        /** @var Card $card */
        $card = $this->cardRepository->find($id);
        if (empty($card)) {
            throw new NotFoundHttpException('Card not found');
        }

        $this->cardRepository->remove($card);
        return [];
    }
}