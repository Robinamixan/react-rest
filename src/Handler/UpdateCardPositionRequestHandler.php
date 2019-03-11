<?php

namespace App\Handler;


use App\Entity\Card;
use App\Entity\Stage;
use App\Repository\CardRepository;
use App\Repository\StageRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

define('WEIGHT_CHANGE', '1');
define('WEIGHT_MOVE_UP', 'up');
define('WEIGHT_MOVE_DOWN', 'down');

define('STAGE_CHANGE', '2');

class UpdateCardPositionRequestHandler
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
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * UpdateCardRequestHandler constructor.
     *
     * @param StageRepository $stageRepository
     * @param CardRepository $cardRepository
     * @param ObjectManager $entityManager
     */
    public function __construct(
        StageRepository $stageRepository,
        CardRepository $cardRepository,
        ObjectManager $entityManager
    ) {
        $this->stageRepository = $stageRepository;
        $this->cardRepository = $cardRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return Card
     */
    public function handle(Request $request)
    {
        $cardId = $request->get('cardId');
        $stageId = $request->get('stageId');

        $actionType = $request->request->get('actionType');
        $action = $request->request->get('action');

        /** @var Stage $stage */
        $stage = $this->stageRepository->find($stageId);
        if (empty($stage)) {
            throw new NotFoundHttpException('Stage not found');
        }

        /** @var Card $card */
        $card = $this->cardRepository->find($cardId);

        if (empty($card)) {
            throw new NotFoundHttpException('Card not found');
        }

        switch ($actionType) {
            case WEIGHT_CHANGE:
                $card = $this->changeCardPosition($card, $stage, $action);
                break;
            case STAGE_CHANGE:
                $card = $this->changeCardStage($card, $action);
                break;
        }

        return $card;
    }

    private function changeCardPosition(Card $card, Stage $stage, string $action)
    {
        $stageCards = $stage->getCards();

        $firstCard = $card;
        $firstWeight = $card->getWeight();

        $secondCard = null;
        $secondWeight = null;

        $weights = [];
        foreach ($stageCards as $stageCard) {
            $weights[$stageCard->getWeight()] = [
                'id' => $stageCard->getId(),
                'weight' => $stageCard->getWeight(),
            ];
        }
        ksort($weights);
        $weights = array_values($weights);

        foreach ($weights as $index => $weight) {
            if ($weight['weight'] === (int) $firstWeight) {
                $counter = $index;
                while (isset($weights[$counter])) {
                    if ($action === WEIGHT_MOVE_UP) {
                        if ($weights[$counter]['weight'] < $firstWeight) {
                            $secondWeight = $weights[$counter]['weight'];
                            $secondCard = $this->cardRepository->find($weights[$counter]['id']);
                            break 2;
                        } else {
                            --$counter;
                        }
                    } else {
                        if ($weights[$counter]['weight'] > $firstWeight) {
                            $secondWeight = $weights[$counter]['weight'];
                            $secondCard = $this->cardRepository->find($weights[$counter]['id']);
                            break 2;
                        } else {
                            ++$counter;
                        }
                    }
                }
            }
        }

        if (!empty($firstCard) && !empty($secondCard)) {
            $firstCard->setWeight($secondWeight);
            $secondCard->setWeight($firstWeight);

            $this->entityManager->persist($firstCard);
            $this->entityManager->persist($secondCard);
            $this->entityManager->flush();
        }

        return $firstCard;
    }

    /**
     * @param Card $card
     * @param string $newStageId
     *
     * @return Card
     */
    private function changeCardStage(Card $card, string $newStageId)
    {
        $stage = $this->stageRepository->find($newStageId);

        /** @var Card $stageCard */
        foreach ($stage->getCards() as $stageCard) {
            $weight = $stageCard->getWeight();
            $stageCard->setWeight($weight + 1);

            $this->entityManager->persist($stageCard);
        }
        $this->entityManager->flush();

        $card = $this->cardRepository->update($card, null, null, $stage, 0);

        return $card;
    }
}