<?php

namespace App\Handler;


use App\Entity\Card;
use App\Entity\Stage;
use App\Repository\CardRepository;
use App\Repository\StageRepository;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     *
     * @return Card
     */
    public function handle(Request $request)
    {
        $title = $request->request->get('title', '');
        $content = $request->request->get('content', '');
        $stageId = $request->get('stageId', '');

        /** @var Stage $stage */
        $stage = $this->stageRepository->find($stageId);
        if (empty($stage)) {
            throw new NotFoundHttpException('Stage not found');
        }

        $weight = $stage->getCardsMaxWeight() + 1;

        return $this->cardRepository->create($title, $content, $stage, $weight);
    }
}