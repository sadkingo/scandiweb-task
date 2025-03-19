<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as Mapping;

/**
 * AttributeItemsEntity class
 *
 * Represents a specific value for a product attribute (e.g., "Red" for Color attribute).
 * Each attribute item belongs to an attribute and contains both a display value and internal value.
 */
#[Mapping\Entity]
#[Mapping\Table(name: 'attribute_items')]
class AttributeItemsEntity
{
    /**
     * @var int The unique identifier for the attribute item
     */
    #[Mapping\Id]
    #[Mapping\GeneratedValue]
    #[Mapping\Column(type: 'integer')]
    private int $id;

    /**
     * @var string The human-readable display value (e.g., "Forest Green")
     */
    #[Mapping\Column(type: 'string', length: 255)]
    private string $displayValue;

    /**
     * @var string The internal value used by the system (e.g., "#228B22")
     */
    #[Mapping\Column(type: 'string', length: 255)]
    private string $value;

    /**
     * @var AttributeEntity|null The attribute this item belongs to
     */
    #[Mapping\ManyToOne(targetEntity: AttributeEntity::class, inversedBy: "attributeItems")]
    #[Mapping\JoinColumn(name: "attribute_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?AttributeEntity $attribute = null;

    /**
     * Get the attribute this item belongs to
     *
     * @return AttributeEntity|null The parent attribute or null if not set
     */
    public function getAttribute(): ?AttributeEntity
    {
        return $this->attribute;
    }

    /**
     * Set the attribute this item belongs to
     *
     * @param AttributeEntity|null $attribute The parent attribute
     * @return void
     */
    public function setAttribute(?AttributeEntity $attribute): void
    {
        $this->attribute = $attribute;
    }

    /**
     * Get the attribute item ID
     *
     * @return int The attribute item ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the display value
     *
     * @return string The human-readable display value
     */
    public function getDisplayValue(): string
    {
        return $this->displayValue;
    }

    /**
     * Set the display value
     *
     * @param string $displayValue The new display value
     * @return void
     */
    public function setDisplayValue(string $displayValue): void
    {
        $this->displayValue = $displayValue;
    }

    /**
     * Get the internal value
     *
     * @return string The internal value
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set the internal value
     *
     * @param string $value The new internal value
     * @return void
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
