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
     * @param bool $doFlash
     *
     * @return Card
     */
    public function create(
        string $title,
        string $content,
        Stage $stage,
        int $weight,
        $doFlash = true
    ): Card {
        $card = new Card($title, $content, $stage, $weight);

        return $this->save($card, $doFlash);
    }

    /**
     * @param Card $card
     * @param string|null $title
     * @param string|null $content
     * @param Stage|null $stage
     * @param int|null $weight
     * @param bool $doFlash
     *
     * @return Card
     */
    public function update(
        Card $card,
        ?string $title = null,
        ?string $content = null,
        ?Stage $stage = null,
        ?int $weight = null,
        $doFlash = true
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

        if ($weight !== null) {
            $card->setWeight($weight);
        }

        return $this->save($card, $doFlash);
    }

    /**
     * @param Card $card
     * @param int $weight
     * @param bool $doFlash
     *
     * @return Card
     */
    public function updateWeight(Card $card, int $weight, $doFlash = true): Card
    {
        $card->setWeight($weight);

        return $this->save($card, $doFlash);
    }

    /**
     * @param Card[] $cards
     * @param bool $doFlash
     */
    public function increaseCardsWeight(array $cards, $doFlash = true): void
    {
        /** @var Card $card */
        foreach ($cards as $card) {
            $card->increaseWeight();

            $this->entityManager->persist($card);
        }
        if ($doFlash) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param Card $card
     * @param bool $doFlash
     *
     * @return Card
     */
    public function save(Card $card, $doFlash = true): Card
    {
        $this->entityManager->persist($card);

        if ($doFlash) {
            $this->entityManager->flush();
        }

        return $card;
    }

    /**
     * @param Card $card
     * @param bool $doFlash
     */
    public function remove(Card $card, $doFlash = true): void
    {
        $this->entityManager->remove($card);

        if ($doFlash) {
            $this->entityManager->flush();
        }
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
