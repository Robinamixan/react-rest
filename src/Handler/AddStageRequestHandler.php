<?php

namespace App\Handler;

use App\Entity\Stage;
use App\Repository\BoardRepository;
use App\Repository\StageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AddStageRequestHandler
{
    /**
     * @var BoardRepository
     */
    private $boardRepository;

    /**
     * @var StageRepository
     */
    private $stageRepository;

    /**
     * @param BoardRepository $boardRepository
     * @param StageRepository $stageRepository
     */
    public function __construct(
        BoardRepository $boardRepository,
        StageRepository $stageRepository
    ) {
        $this->boardRepository = $boardRepository;
        $this->stageRepository = $stageRepository;
    }

    /**
     * @param Request $request
     *
     * @return Stage
     *
     * @throws NotFoundHttpException
     */
    public function handle(Request $request): Stage
    {
        $title = $request->request->get('title', '');
        $boardId = $request->request->get('idBoard', '');

        $board = $this->boardRepository->find($boardId);

        if (!$board) {
            throw new NotFoundHttpException('Board not found');
        }

        $stage = $this->stageRepository->create($title, $board);

        $this->stageRepository->flush();

        return $stage;
    }
}
