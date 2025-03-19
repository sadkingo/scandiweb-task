<?php

namespace App\Entities;

use Doctrine\ORM\Mapping;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * CurrencyEntity class
 * 
 * Represents a currency in the system.
 * Each currency can be associated with multiple product prices.
 */
#[Mapping\Entity]
#[Mapping\Table(name: 'currencies')]
class CurrencyEntity
{
    /**
     * @var int The unique identifier for the currency
     */
    #[Mapping\Id]
    #[Mapping\GeneratedValue]
    #[Mapping\Column(type: 'integer')]
    private int $id;

    /**
     * @var string The currency label (e.g., USD, EUR)
     */
    #[Mapping\Column(length: 3)]
    private string $label;

    /**
     * @var string The currency symbol (e.g., $, €)
     */
    #[Mapping\Column(length: 1)]
    private string $symbol;

    /**
     * @var Collection Collection of prices using this currency
     */
    #[Mapping\OneToMany(targetEntity: PriceEntity::class, mappedBy: "currency", cascade: ["persist", "remove"])]
    private Collection $prices; // ✅ KEEP ONLY THIS ONE

    /**
     * Constructor
     * 
     * Initializes the prices collection
     */
    public function __construct()
    {
        $this->prices = new ArrayCollection();
    }

    /**
     * Get all prices using this currency
     * 
     * @return Collection Collection of prices
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    /**
     * Add a price to this currency
     * 
     * @param PriceEntity $price The price to add
     * @return void
     */
    public function addPrice(PriceEntity $price): void
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
            $price->setCurrency($this);
        }
    }

    /**
     * Get the currency ID
     * 
     * @return int The currency ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the currency label
     * 
     * @return string The currency label
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get the currency symbol
     * 
     * @return string The currency symbol
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * Set the currency label
     * 
     * @param string $label The new currency label
     * @return self
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Set the currency symbol
     * 
     * @param string $symbol The new currency symbol
     * @return self
     */
    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;
        return $this;
    }
}
