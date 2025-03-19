<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * OrderItemEntity class
 *
 * Represents an individual item in a customer order.
 * Contains product reference, quantity, price, and selected attributes.
 */
#[ORM\Entity]
#[ORM\Table(name: 'order_items')]
class OrderItemEntity
{
    /**
     * @var int The unique identifier for the order item
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    /**
     * @var OrderEntity The order this item belongs to
     */
    #[ORM\ManyToOne(targetEntity: OrderEntity::class, inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private OrderEntity $order;

    /**
     * @var ProductEntity The product in this order item
     */
    #[ORM\ManyToOne(targetEntity: ProductEntity::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    private ProductEntity $product;

    /**
     * @var int The quantity of the product ordered
     */
    #[ORM\Column(type: 'integer')]
    private int $quantity = 1;

    /**
     * @var string The unit price of the product at time of order
     */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $unitPrice = '0.00';

    /**
     *  attribute items for this product
     */
    #[ORM\Column(length: 255)]
    protected string $selectedAttributes;

    /**
     * Get the order item ID
     *
     * @return int The order item ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the order this item belongs to
     *
     * @return OrderEntity The parent order
     */
    public function getOrder(): OrderEntity
    {
        return $this->order;
    }

    /**
     * Set the order this item belongs to
     *
     * @param OrderEntity $order The parent order
     * @return self
     */
    public function setOrder(OrderEntity $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Get the product in this order item
     *
     * @return ProductEntity The product
     */
    public function getProduct(): ProductEntity
    {
        return $this->product;
    }

    /**
     * Set the product for this order item
     *
     * @param ProductEntity $product The product
     * @return self
     */
    public function setProduct(ProductEntity $product): self
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get the quantity of the product
     *
     * @return int The quantity
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Set the quantity of the product
     *
     * @param int $quantity The new quantity
     * @return self
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Get the unit price of the product
     *
     * @return float The unit price
     */
    public function getUnitPrice(): float
    {
        return (float)$this->unitPrice;
    }

    /**
     * Set the unit price of the product
     *
     * @param float $unitPrice The new unit price
     * @return self
     */
    public function setUnitPrice(float $unitPrice): self
    {
        $this->unitPrice = (string)$unitPrice;
        return $this;
    }

    /**
     * Get the total price for this order item
     *
     * @return float The total price
     */
    public function getTotalPrice(): float
    {
        return $this->getUnitPrice() * $this->getQuantity();
    }

    /**
     * Get the selected attributes for this product
     *
     * @return array The selected attributes
     */
    public function getSelectedAttributes(): array
    {
        return json_decode($this->selectedAttributes);
    }

    /**
     * Set the selected attributes for this product
     *
     * @param string $selectedAttributes The new selected attributes
     * @return self
     */
    public function setSelectedAttributes(string $selectedAttributes): self
    {
        $this->selectedAttributes = $selectedAttributes;
        return $this;
    }

    /**
     * Add a selected attribute item to this order item
     *
     * @param AttributeItemsEntity $attributeItem The attribute item to add
     * @return self
     */
    public function addSelectedAttribute(AttributeItemsEntity $attributeItem): self
    {
        //TODO: make addSelectedAttribute
        return $this;
    }

    /**
     * Remove a selected attribute item from this order item
     *
     * @param AttributeItemsEntity $attributeItem The attribute item to remove
     * @return self
     */
    public function removeSelectedAttribute(AttributeItemsEntity $attributeItem): self
    {
//TODO: remove attributes
        return $this;
    }
}