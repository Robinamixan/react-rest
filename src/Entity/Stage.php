<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StageRepository")
 */
class Stage
{
    /**
     * @JMS\Type("string")
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", nullable=false)
     */
    private $id;

    /**
     * @JMS\Type("string")
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @JMS\Exclude()
     * @ORM\ManyToOne(targetEntity="App\Entity\Board", inversedBy="stages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $board;

    /**
     * @JMS\Exclude()
     * @ORM\OneToMany(targetEntity="App\Entity\Card", mappedBy="stage")
     */
    private $cards;

    /**
     * Stage constructor.
     */
    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Stage
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Board
     */
    public function getBoard(): Board
    {
        return $this->board;
    }

    /**
     * @param Board $board
     *
     * @return Stage
     */
    public function setBoard(Board $board): self
    {
        $this->board = $board;

        return $this;
    }

    /**
     * @return Card[]
     */
    public function getCards(): array
    {
        return $this->cards->toArray();
    }

    /**
     * @param Card $card
     *
     * @return Stage
     */
    public function addCard(Card $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards[] = $card;
            $card->setStage($this);
        }

        return $this;
    }

    /**
     * @param Card $card
     *
     * @return Stage
     */
    public function removeCard(Card $card): self
    {
        if ($this->cards->contains($card)) {
            $this->cards->removeElement($card);
            // set the owning side to null (unless already changed)
            if ($card->getStage() === $this) {
                $card->setStage(null);
            }
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getCardsMaxWeight(): int
    {
        $maxWeight = 0;
        foreach ($this->cards as $card) {
            $cardWeight = $card->getWeight();
            if ($cardWeight > $maxWeight) {
                $maxWeight = $cardWeight;
            }
        }

        return $maxWeight;
    }
}
