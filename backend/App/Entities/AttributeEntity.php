<?php

namespace App\Entities;

use Doctrine\ORM\Mapping;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * AttributeEntity class
 * 
 * Represents a product attribute in the system (e.g., color, size).
 * Each attribute can have multiple attribute items (e.g., red, blue for color).
 */
#[Mapping\Entity]
#[Mapping\Table(name: 'attributes')]
class AttributeEntity
{
    /**
     * @var int The unique identifier for the attribute
     */
    #[Mapping\Id]
    #[Mapping\GeneratedValue]
    #[Mapping\Column(type: 'integer')]
    private int $id;

    /**
     * @var string The name of the attribute (e.g., "Color", "Size")
     */
    #[Mapping\Column(type: 'string', length: 255)]
    private string $name;

    /**
     * @var string The type of the attribute (e.g., "swatch", "text")
     */
    #[Mapping\Column(type: 'string', length: 255)]
    private string $type;

    /**
     * @var Collection Collection of attribute items belonging to this attribute
     */
    #[Mapping\OneToMany(targetEntity: AttributeItemsEntity::class, mappedBy: "attribute", cascade: [
        "persist",
        "remove"
    ])]
    private Collection $attributeItems;

    /**
     * @var ProductEntity The product this attribute belongs to
     */
    #[Mapping\ManyToOne(targetEntity: ProductEntity::class, inversedBy: "attributes")]
    #[Mapping\JoinColumn(name: "product_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ProductEntity $product;

    /**
     * Constructor
     * 
     * Initializes the attribute items collection
     */
    public function __construct()
    {
        $this->attributeItems = new ArrayCollection();
    }

    /**
     * Get all attribute items
     * 
     * @return Collection Collection of attribute items
     */
    public function getAttributeItems(): Collection
    {
        return $this->attributeItems;
    }

    /**
     * Add an attribute item to this attribute
     * 
     * @param AttributeItemsEntity $attributeItem The attribute item to add
     * @return void
     */
    public function addAttributeItem(AttributeItemsEntity $attributeItem): void
    {
        if (!$this->attributeItems->contains($attributeItem)) {
            $this->attributeItems->add($attributeItem);
            $attributeItem->setAttribute($this); // Ensure reverse relation is set
        }
    }

    /**
     * Get all attribute items as an iterable
     * 
     * @return AttributeItemsEntity[]
     */
    public function getItems(): iterable
    {
        return $this->attributeItems;
    }

    /**
     * Get the attribute ID
     * 
     * @return int The attribute ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the attribute name
     * 
     * @return string The attribute name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the attribute name
     * 
     * @param string $name The new attribute name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the attribute type
     * 
     * @return string The attribute type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the attribute type
     * 
     * @param string $type The new attribute type
     * @return void
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
