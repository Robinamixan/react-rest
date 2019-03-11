<?php

namespace App\Handler;


use App\Entity\Card;
use App\Repository\CardRepository;
use App\Repository\StageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateCardRequestHandler
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
     * UpdateCardRequestHandler constructor.
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

    public function handle(Request $request)
    {
        $stageId = $request->get('stageId');
        $cardId = $request->get('cardId');
        $title = $request->request->get('title');
        $content = $request->request->get('content');

        $stage = $this->stageRepository->find($stageId);
        if (empty($stage)) {
            throw new NotFoundHttpException('Stage not found');
        }

        /** @var Card $card */
        $card = $this->cardRepository->find($cardId);

        if (empty($card)) {
            throw new NotFoundHttpException('Card not found');
        }

        return $this->cardRepository->update($card, $title, $content, $stage);
    }
}