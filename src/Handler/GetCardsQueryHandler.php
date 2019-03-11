<?php

namespace App\Handler;


use App\Repository\StageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetCardsQueryHandler
{
    /**
     * @var StageRepository
     */
    private $stageRepository;

    /**
     * GetCardsQueryHandler constructor.
     *
     * @param StageRepository $stageRepository
     */
    public function __construct(
        StageRepository $stageRepository
    ){
        $this->stageRepository = $stageRepository;
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function handle(Request $request)
    {
        $stageId = $request->get('stageId');
        $stage = $this->stageRepository->findOneBy(['id' => $stageId]);
        if (empty($stage)) {
            throw new NotFoundHttpException('Stage not found');
        }

        $cards = $stage->getCards();
        if (empty($cards)) {
            throw new NotFoundHttpException('Cards not found');
        }

        return $cards;
    }
}