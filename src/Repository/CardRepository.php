<?php

namespace App\Repository;

use App\Entity\Card;
use App\Entity\Stage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CardRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(RegistryInterface $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Card::class);

        $this->entityManager = $entityManager;
    }

    /**
     * @param string $title
     * @param string $content
     * @param Stage $stage
     * @param int $weight
     *
     * @return Card
     */
    public function create(
        string $title,
        string $content,
        Stage $stage,
        int $weight
    ): Card {
        $card = new Card($title, $content, $stage, $weight);

        return $this->save($card);
    }

    /**
     * @param Card $card
     *
     * @return Card
     */
    public function save(Card $card): Card
    {
        $this->entityManager->persist($card);
        $this->entityManager->flush();

        return $card;
    }

    /**
     * @param Card $card
     * @param string|null $title
     * @param string|null $content
     * @param Stage|null $stage
     * @param int|null $weight
     *
     * @return Card
     */
    public function update(
        Card $card,
        ?string $title = null,
        ?string $content = null,
        ?Stage $stage = null,
        ?int $weight = null
    ): Card {
        if ($title !== null && $title !== '') {
            $card->setTitle($title);
        }

        if ($content !== null && $title !== '') {
            $card->setContent($content);
        }

        if ($stage !== null) {
            $card->setStage($stage);
        }

        if ($weight!== null) {
            $card->setWeight($weight);
        }

        return $this->save($card);
    }

    /**
     * @param Card $card
     * @param int $weight
     *
     * @return Card
     */
    public function updateWeight(Card $card, int $weight): Card
    {
        $card->setWeight($weight);

        return $this->save($card);
    }

    /**
     * @param Card[] $cards
     */
    public function increaseCardsWeight(array $cards): void
    {
        /** @var Card $card */
        foreach ($cards as $card) {
            $card->increaseWeight();

            $this->entityManager->persist($card);
        }
        $this->entityManager->flush();
    }

    /**
     * @param Card $card
     */
    public function remove(Card $card): void
    {
        $this->entityManager->remove($card);
        $this->entityManager->flush();
    }
}
