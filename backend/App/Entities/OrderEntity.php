<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * OrderEntity class
 *
 * Represents a customer order in the system.
 * Contains ordered products, total amount, currency, and creation timestamp.
 */
#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class OrderEntity
{
    /**
     * @var int The unique identifier for the order
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    /**
     * @var string The total amount of the order as a string for precision
     */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $total = '0.00';

    /**
     * @var CurrencyEntity The currency used for this order
     */
    #[ORM\ManyToOne(targetEntity: CurrencyEntity::class)]
    #[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'id')]
    private CurrencyEntity $currency;

    /**
     * @var Collection Collection of order items for this order
     */
    #[ORM\OneToMany(targetEntity: OrderItemEntity::class, mappedBy: 'order', cascade: ['persist', 'remove'])]
    private Collection $items;

    /**
     * @var \DateTime The timestamp when the order was created
     */
    #[ORM\Column(type: 'datetime')]
    private \DateTime $created_at;

    /**
     * Constructor
     *
     * Initializes the creation timestamp and items collection
     */
    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->items = new ArrayCollection();
    }

    /**
     * Get the order ID
     *
     * @return int The order ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get all ordered products
     *
     * @return array Array of ordered products
     */
    public function getOrderedProducts(): array
    {
        $products = [];

        foreach ($this->items as $item) {
            $products[] = [
                'product_id' => $item->getProduct()->getId(),
                'quantity' => $item->getQuantity(),
                'unit_price' => $item->getUnitPrice(),
                'total' => $item->getTotalPrice(),
                'selected_attributes' => $item->getSelectedAttributes()
            ];
        }

        return $products;
    }

    /**
     * Get all order items
     *
     * @return Collection Collection of order items
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * Add an item to the order
     *
     * @param OrderItemEntity $item The item to add
     * @return self
     */
    public function addItem(OrderItemEntity $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrder($this);
            $this->updateTotal();
        }

        return $this;
    }

    /**
     * Remove an item from the order
     *
     * @param OrderItemEntity $item The item to remove
     * @return self
     */
    public function removeItem(OrderItemEntity $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            $this->updateTotal();
        }

        return $this;
    }

    /**
     * Add a product to the order
     *
     * @param ProductEntity $product The product to add
     * @param int $quantity The quantity of the product
     * @param array $selectedAttributes Optional attributes selected for the product
     * @return self
     */
    public function addProduct(ProductEntity $product, int $quantity, array $selectedAttributes = []): self
    {
        $unitPrice = $product->getPrices()[0]->getAmount();

        $orderItem = new OrderItemEntity();
        $orderItem->setProduct($product);
        $orderItem->setQuantity($quantity);
        $orderItem->setUnitPrice($unitPrice);
        $orderItem->setSelectedAttributes(json_encode($selectedAttributes));
        $orderItem->setOrder($this);

        $this->addItem($orderItem);
        $this->updateTotal();

        return $this;
    }

    /**
     * Remove a product from the order
     *
     * @param string $productId The ID of the product to remove
     * @return self
     */
    public function removeProduct(string $productId): self
    {
        foreach ($this->items as $item) {
            if ($item->getProduct()->getId() === $productId) {
                $this->removeItem($item);
                return $this;
            }
        }
        return $this;
    }

    /**
     * Get the total amount of the order
     *
     * @return float The total amount
     */
    public function getTotal(): float
    {
        return (float)$this->total;
    }

    /**
     * Set the total amount of the order
     *
     * @param float $total The new total amount
     * @return self
     */
    public function setTotal(float $total): self
    {
        $this->total = (string)$total;
        return $this;
    }

    /**
     * Get the currency used for this order
     *
     * @return CurrencyEntity The currency
     */
    public function getCurrency(): CurrencyEntity
    {
        return $this->currency;
    }

    /**
     * Set the currency for this order
     *
     * @param CurrencyEntity $currency The new currency
     * @return self
     */
    public function setCurrency(CurrencyEntity $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Get the creation timestamp
     *
     * @return \DateTime The creation timestamp
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    /**
     * Update the total amount based on order items
     *
     * @return void
     */
    private function updateTotal(): void
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getTotalPrice();
        }
        $this->total = (string)$total;
    }
}