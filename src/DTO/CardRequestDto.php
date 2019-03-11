<?php

namespace App\DTO;

use App\Entity\Card;
use App\Entity\Stage;
use JMS\Serializer\Annotation as JMS;

class CardRequestDto
{
    /**
     * @var Stage|null
     */
    private $stage;

    /**
     * @var Card|null
     */
    private $card;

    /**
     * @var string|null
     * @JMS\Type("string")
     */
    private $title;

    /**
     * @var string|null
     * @JMS\Type("string")
     */
    private $content;

    /**
     * @var string|null
     * @JMS\Type("string")
     */
    public $move;

    /**
     * @var string|null
     * @JMS\Type("string")
     */
    private $action;

    /**
     * @return Stage|null
     */
    public function getStage(): ?Stage
    {
        return $this->stage;
    }

    /**
     * @param Stage|null $stage
     */
    public function setStage(?Stage $stage): void
    {
        $this->stage = $stage;
    }

    /**
     * @return Card|null
     */
    public function getCard(): ?Card
    {
        return $this->card;
    }

    /**
     * @param Card|null $card
     */
    public function setCard(?Card $card): void
    {
        $this->card = $card;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getMove(): ?string
    {
        return $this->move;
    }

    /**
     * @return string|null
     */
    public function getAction(): ?string
    {
        return $this->action;
    }
}
