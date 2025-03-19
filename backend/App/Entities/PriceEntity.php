<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping;

/**
 * PriceEntity class
 * 
 * Represents a product price in the system.
 * Each price is associated with a product and a currency.
 */
#[Mapping\Entity]
#[Mapping\Table(name: 'prices')]
class PriceEntity
{
    /**
     * @var int The unique identifier for the price
     */
    #[Mapping\Id]
    #[Mapping\GeneratedValue]
    #[Mapping\Column(type: 'integer')]
    private int $id;
    
    /**
     * @var string The price amount stored as string for precision
     */
    #[Mapping\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $amount;
    
    /**
     * @var string The ID of the product this price belongs to
     */
    #[Mapping\Column(length: 255)]
    protected string $product_id;
    
    /**
     * @var ProductEntity The product this price belongs to
     */
    #[Mapping\ManyToOne(targetEntity: ProductEntity::class, inversedBy: 'prices')] // FIXED HERE
    #[Mapping\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ProductEntity $product;
    
    /**
     * @var CurrencyEntity|null The currency of this price
     */
    #[Mapping\ManyToOne(targetEntity: CurrencyEntity::class, inversedBy: "prices")]
    #[Mapping\JoinColumn(name: "currency_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?CurrencyEntity $currency = null;
    
    /**
     * Get the currency of this price
     * 
     * @return CurrencyEntity|null The currency or null if not set
     */
    public function getCurrency(): ?CurrencyEntity
    {
        return $this->currency;
    }
    
    /**
     * Set the currency of this price
     * 
     * @param CurrencyEntity|null $currency The currency
     * @return void
     */
    public function setCurrency(?CurrencyEntity $currency): void
    {
        $this->currency = $currency;
    }
    
    /**
     * Get the price ID
     * 
     * @return int The price ID
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * Get the price amount
     * 
     * @return string The price amount as string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }
    
    /**
     * Set the price amount
     * 
     * @param string $amount The new price amount
     * @return self
     */
    public function setAmount(string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }
    
    /**
     * Get the product ID
     * 
     * @return string The product ID
     */
    public function getProductId(): string
    {
        return $this->product_id;
    }
    
    /**
     * Set the product ID
     * 
     * @param string $product_id The new product ID
     * @return void
     */
    public function setProductId(string $product_id): void
    {
        $this->product_id = $product_id;
    }
    
    /**
     * Get the product this price belongs to
     * 
     * @return ProductEntity The product
     */
    public function getProduct(): ProductEntity
    {
        return $this->product;
    }
    
    /**
     * Set the product this price belongs to
     * 
     * @param ProductEntity $product The product
     * @return void
     */
    public function setProduct(ProductEntity $product): void
    {
        $this->product = $product;
    }
}
