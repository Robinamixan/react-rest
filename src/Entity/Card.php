<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CardRepository")
 */
class Card
{
    /**
     * @JMS\Type("string")
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @JMS\Type("string")
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @JMS\Type("string")
     *
     * @ORM\Column(type="string", length=580, nullable=true)
     */
    private $content;

    /**
     * @JMS\Exclude()
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Stage", inversedBy="cards")
     * @ORM\JoinColumn(nullable=false)
     */
    private $stage;

    /**
     * @JMS\Type("string")
     *
     * @ORM\Column(type="integer")
     */
    private $weight;

    /**
     * @param string $title
     * @param string $content
     * @param Stage $stage
     * @param int $weight
     */
    public function __construct(string $title, string $content, Stage $stage, int $weight = 0)
    {
        $this->setTitle($title);
        $this->setContent($content);
        $this->setStage($stage);
        $this->setWeight($weight);
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
     * @param string|null $title
     *
     * @return Card
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     *
     * @return Card
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Stage
     */
    public function getStage(): Stage
    {
        return $this->stage;
    }

    /**
     * @param Stage $stage
     *
     * @return Card
     */
    public function setStage(Stage $stage): self
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     *
     * @return Card
     */
    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return Card
     */
    public function increaseWeight(): self
    {
        ++$this->weight;

        return $this;
    }
}
