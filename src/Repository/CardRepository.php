<?php

namespace App\Repository;

use App\Entity\Card;
use App\Entity\Stage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CardRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Card::class);
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
     * @param \App\Entity\Card $card
     *
     * @return \App\Entity\Card
     */
    public function save(Card $card): Card
    {
        $this->_em->persist($card);
        $this->_em->flush();

        return $card;
    }

    /**
     * @param Card $card
     * @param null|string $title
     * @param null|string $content
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
        if (!is_null($title) && $title !== '') {
            $card->setTitle($title);
        }

        if (!is_null($content) && $title !== '') {
            $card->setContent($content);
        }

        if (!is_null($stage)) {
            $card->setStage($stage);
        }

        if (!is_null($weight)) {
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
    public function increaseCardsWeight(array $cards)
    {
        /** @var Card $card */
        foreach ($cards as $card) {
            $weight = $card->getWeight();
            $card->setWeight($weight + 1);

            $this->_em->persist($card);
        }
        $this->_em->flush();
    }

    /**
     * @param \App\Entity\Card $card
     */
    public function remove(Card $card)
    {
        $this->_em->remove($card);
        $this->_em->flush();
    }
}
