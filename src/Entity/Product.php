<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

//* @UniqueEntity("productCode")

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="tbl_product_data")
 * @Assert\Expression(
 *     "this.getProductStock() >= 10 or this.getProductCost() >= 5",
 *     message="Any stock item which costs less that $5 and has less than 10 stock will not be imported."
 * )
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", name="intProductDataId")
     */
    private $productId;

    /**
     * @ORM\Column(type="string", name="strProductName", length=50, nullable=false)
     * @Assert\NotBlank()
     */
    private $productName;

    /**
     * @ORM\Column(type="string", name="strProductDesc", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $productDesc;

    /**
     * @ORM\Column(type="string", name="strProductCode", length=10, nullable=false, unique=true)
     * @Assert\NotBlank()
     */
    private $productCode;

    /**
     * @ORM\Column(type="integer", name="intProductStock", nullable=false, options={"unsigned"=true})
     * @Assert\NotBlank()
     */
    private $productStock;

    /**
     * @ORM\Column(type="decimal",
     *     precision=8,
     *     scale=2,
     *     name="floatProductCost",
     *     nullable=false,
     *     options={"unsigned"=true}
     *     )
     * @Assert\NotBlank()
     * @Assert\Range(max=1000)
     */
    private $productCost;

    /**
     * @ORM\Column(type="datetime", name="dtmAdded", nullable=true, options={"default" : null})
     */
    private $addedDate;

    /**
     * @ORM\Column(type="datetime", name="dtmDiscontinued", nullable=true, options={"default" : null})
     */
    private $discontinuedDate;

    /**
     * @ORM\Column(type="datetime", name="stmTimestamp", nullable=false)
     * @ORM\Version
     */
    private $timestamp;

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * @param string $productName
     */
    public function setProductName(string $productName): void
    {
        $this->productName = $productName;
    }

    /**
     * @return string
     */
    public function getProductDesc(): string
    {
        return $this->productDesc;
    }

    /**
     * @param string $productDesc
     */
    public function setProductDesc(string $productDesc): void
    {
        $this->productDesc = $productDesc;
    }

    /**
     * @return string
     */
    public function getProductCode(): string
    {
        return $this->productCode;
    }

    /**
     * @param string $productCode
     */
    public function setProductCode(string $productCode): void
    {
        $this->productCode = $productCode;
    }

    /**
     * @return \DateTime
     */
    public function getAddedDate(): ?\DateTime
    {
        return $this->addedDate;
    }

    /**
     * @param \DateTime $addedDate
     */
    public function setAddedDate(\DateTime $addedDate): void
    {
        $this->addedDate = $addedDate;
    }

    /**
     * @return \DateTime
     */
    public function getDiscontinuedDate(): ?\DateTime
    {
        return $this->discontinuedDate;
    }

    /**
     * @param \DateTime $discontinuedDate
     */
    public function setDiscontinuedDate(\DateTime $discontinuedDate): void
    {
        $this->discontinuedDate = $discontinuedDate;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp(): ?\DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setTimestamp(\DateTime $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return int
     */
    public function getProductStock(): int
    {
        return $this->productStock;
    }

    /**
     * @param int $productStock
     */
    public function setProductStock(int $productStock): void
    {
        $this->productStock = $productStock;
    }

    /**
     * @return float
     */
    public function getProductCost(): float
    {
        return $this->productCost;
    }

    /**
     * @param float $productCost
     */
    public function setProductCost(float $productCost): void
    {
        $this->productCost = $productCost;
    }
}
