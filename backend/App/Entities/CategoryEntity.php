<?php

namespace App\Entities;

use Doctrine\ORM\Mapping;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * CategoryEntity class
 * 
 * Represents a product category in the system.
 * Each category can contain multiple products.
 */
#[Mapping\Entity]
#[Mapping\Table(name: 'categories')]
class CategoryEntity
{
    /**
     * @var int The unique identifier for the category
     */
    #[Mapping\Id]
    #[Mapping\GeneratedValue]
    #[Mapping\Column(type: 'integer')]
    private int $id;
    
    /**
     * @var string The name of the category
     */
    #[Mapping\Column(length: 255)]
    private string $name;
    
    /**
     * @var Collection Collection of products belonging to this category
     */
    #[Mapping\OneToMany(targetEntity: ProductEntity::class, mappedBy: "category", cascade: ["persist", "remove"])]
    private Collection $products;
    
    /**
     * Constructor
     * 
     * Initializes the products collection
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
    }
    
    /**
     * Get all products in this category
     * 
     * @return Collection Collection of products
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }
    
    /**
     * Add a product to this category
     * 
     * @param ProductEntity $product The product to add
     * @return void
     */
    public function addProduct(ProductEntity $product): void
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setCategory($this);
        }
    }
    
    /**
     * Remove a product from this category
     * 
     * @param ProductEntity $product The product to remove
     * @return self
     */
    public function removeProduct(ProductEntity $product): self
    {
        if ($this->products->removeElement($product)) {
            if ($product->getCategory() === $this) {
                $this->products->removeElement($product);
            }
        }
        return $this;
    }
    
    /**
     * Get the category ID
     * 
     * @return int The category ID
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * Get the category name
     * 
     * @return string The category name
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Set the category name
     * 
     * @param string $name The new category name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
