<?php

namespace App\Repository;

use App\Entity\Board;
use App\Entity\Card;
use App\Entity\Stage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class StageRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(RegistryInterface $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Stage::class);

        $this->entityManager = $entityManager;
    }

    /**
     * @param Stage $stage
     *
     * @return int
     */
    public function getCardsMaxWeight(Stage $stage): int
    {
        $weights = array_map(
            function (Card $card): int {
                return $card->getWeight();
            },
            $stage->getCards()
        );

        return !empty($weights) ? max($weights) : 0;
    }

    /**
     * @param string $title
     * @param Board $board
     *
     * @return Stage
     */
    public function create(
        string $title,
        Board $board
    ): Stage {
        $stage = new Stage($title, $board);

        return $this->save($stage);
    }

    /**
     * @param Stage $stage
     *
     * @return Stage
     */
    public function save(Stage $stage): Stage
    {
        $this->entityManager->persist($stage);

        return $stage;
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
