<?php

namespace App\Handler;

use App\DTO\CardRequestDto;
use App\Entity\Card;
use App\Entity\Stage;
use App\Repository\CardRepository;
use App\Repository\StageRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

define('WEIGHT_CHANGE', 'change_weight');
define('WEIGHT_MOVE_UP', 'up');
define('WEIGHT_MOVE_DOWN', 'down');

define('STAGE_CHANGE', 'change_stage');

class CardRequestHandler
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
     *
     * @throws NotFoundHttpException
     */
    public function handleAddRequest(CardRequestDto $dto): Card
    {
        $stage = $dto->getStage();
        if (empty($stage)) {
            throw new NotFoundHttpException('Stage not found');
        }

        $weight = $this->stageRepository->getCardsMaxWeight($stage) + 1;

        $card = $this->cardRepository->create($dto->getTitle(), $dto->getContent(), $stage, $weight);
        $this->cardRepository->flush();

        return $card;
    }

    /**
     * @param CardRequestDto $dto
     *
     * @throws NotFoundHttpException
     */
    public function handleDeleteRequest(CardRequestDto $dto): void
    {
        $card = $dto->getCard();
        if (empty($card)) {
            throw new NotFoundHttpException('Card not found');
        }

        $this->cardRepository->remove($card);
        $this->cardRepository->flush();
    }

    /**
     * @param CardRequestDto $dto
     *
     * @return Card
     *
     * @throws NotFoundHttpException
     */
    public function handleUpdatePositionRequest(CardRequestDto $dto): Card
    {
        $stage = $dto->getStage();
        if (empty($stage)) {
            throw new NotFoundHttpException('Stage not found');
        }

        $card = $dto->getCard();
        if (empty($card)) {
            throw new NotFoundHttpException('Card not found');
        }

        if ($dto->getMove() === WEIGHT_CHANGE) {
            $card = $this->changeCardPosition($card, $stage, $dto->getAction());
        } elseif ($dto->getMove() === STAGE_CHANGE) {
            $card = $this->changeCardStage($card, $dto->getAction());
        }

        $this->cardRepository->flush();

        return $card;
    }

    /**
     * @param CardRequestDto $dto
     *
     * @return Card
     *
     * @throws NotFoundHttpException
     */
    public function handleUpdateRequest(CardRequestDto $dto): Card
    {
        $stage = $dto->getStage();
        if (empty($stage)) {
            throw new NotFoundHttpException('Stage not found');
        }

        $card = $dto->getCard();
        if (empty($card)) {
            throw new NotFoundHttpException('Card not found');
        }

        $updatedCard = $this->cardRepository->update($card, $dto->getTitle(), $dto->getContent(), $stage);

        $this->cardRepository->flush();

        return $updatedCard;
    }

    /**
     * @param Card $card
     * @param Stage $stage
     * @param string $action
     *
     * @return Card
     */
    private function changeCardPosition(Card $card, Stage $stage, string $action): Card
    {
        $stageCards = $stage->getCards();

        $index = array_search($card, $stageCards);
        if ($index !== false) {
            if ($action === WEIGHT_MOVE_UP) {
                $replaceIndex = --$index;
            } else {
                $replaceIndex = ++$index;
            }

            if (key_exists($replaceIndex, $stageCards)) {
                $originalWeight = $card->getWeight();
                $replaceWeight = $stageCards[$replaceIndex]->getWeight();

                $this->cardRepository->updateWeight($card, $replaceWeight);
                $this->cardRepository->updateWeight($stageCards[$replaceIndex], $originalWeight);
            }
        }

        return $card;
    }

    /**
     * @param Card $card
     * @param string $newStageId
     *
     * @return Card
     */
    private function changeCardStage(Card $card, string $newStageId): Card
    {
        /** @var Stage $stage */
        $stage = $this->stageRepository->find($newStageId);

        $this->cardRepository->increaseCardsWeight($stage->getCards());

        $card = $this->cardRepository->update($card, null, null, $stage, 0);

        return $card;
    }
}
