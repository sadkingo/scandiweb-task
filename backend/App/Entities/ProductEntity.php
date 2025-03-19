<?php

namespace App\Entities;

use Doctrine\ORM\Mapping;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * ProductEntity class
 * 
 * Represents a product in the system.
 * Contains product details, gallery images, attributes, prices, and category.
 */
#[Mapping\Entity]
#[Mapping\Table(name: 'products')]
class ProductEntity
{
    /**
     * @var string The unique identifier for the product
     */
    #[Mapping\Id]
    #[Mapping\Column(type: 'string', length: 255)]
    protected string $id;
    
    /**
     * @var string The name of the product
     */
    #[Mapping\Column(length: 255)]
    protected string $name;
    
    /**
     * @var bool Whether the product is in stock
     */
    #[Mapping\Column(type: 'boolean')]
    protected bool $inStock = false;
    
    /**
     * @var string|null The product description
     */
    #[Mapping\Column(type: 'text', nullable: true)]
    protected ?string $description = null;
    
    /**
     * @var string|null The brand of the product
     */
    #[Mapping\Column(length: 255, nullable: true)]
    protected ?string $brand = null;
    
    /**
     * @var Collection Collection of gallery images for this product
     */
    #[Mapping\OneToMany(targetEntity: GalleryEntity::class, mappedBy: 'product', cascade: ['persist', 'remove'])]
    private Collection $gallery;
    
    /**
     * @var Collection Collection of attributes for this product
     */
    #[Mapping\OneToMany(targetEntity: AttributeEntity::class, mappedBy: 'product', cascade: ['persist', 'remove'])]
    private Collection $attributes;
    
    /**
     * @var Collection Collection of prices for this product
     */
    #[Mapping\OneToMany(targetEntity: PriceEntity::class, mappedBy: "product", cascade: ["persist", "remove"])]
    private Collection $prices;
    
    /**
     * @var CategoryEntity|null The category this product belongs to
     */
    #[Mapping\ManyToOne(targetEntity: CategoryEntity::class, inversedBy: "products")]
    #[Mapping\JoinColumn(name: "category_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?CategoryEntity $category = null;
    
    /**
     * Constructor
     * 
     * Initializes the attributes, gallery, and prices collections
     */
    public function __construct()
    {
        $this->attributes = new ArrayCollection();
        $this->gallery = new ArrayCollection();
        $this->prices = new ArrayCollection();
    }
    
    /**
     * Get all prices for this product
     * 
     * @return Collection Collection of prices
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }
    
    /**
     * Add a price to this product
     * 
     * @param PriceEntity $price The price to add
     * @return void
     */
    public function addPrice(PriceEntity $price): void
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
            $price->setProduct($this);
        }
    }
    
    /**
     * Get the product ID
     * 
     * @return string The product ID
     */
    public function getId(): string
    {
        return $this->id;
    }
    
    /**
     * Get the product name
     * 
     * @return string The product name
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Set the product name
     * 
     * @param string $name The new product name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Check if the product is in stock
     * 
     * @return bool True if in stock, false otherwise
     */
    public function isInStock(): bool
    {
        return $this->inStock;
    }
    
    /**
     * Set the product's stock status
     * 
     * @param bool $inStock The new stock status
     * @return self
     */
    public function setInStock(bool $inStock): self
    {
        $this->inStock = $inStock;
        return $this;
    }
    
    /**
     * Get the product description
     * 
     * @return string|null The product description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    /**
     * Set the product description
     * 
     * @param string|null $description The new product description
     * @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Get the product brand
     * 
     * @return string|null The product brand
     */
    public function getBrand(): ?string
    {
        return $this->brand;
    }
    
    /**
     * Set the product brand
     * 
     * @param string|null $brand The new product brand
     * @return self
     */
    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;
        return $this;
    }
    
    /**
     * Get the product gallery
     * 
     * @return Collection Collection of gallery images
     */
    public function getGallery(): Collection
    {
        return $this->gallery;
    }
    
    /**
     * Add an image to the product gallery
     * 
     * @param string $imageUrl The URL of the image
     * @return self
     */
    public function addGalleryImage(string $imageUrl): self
    {
        $image = new GalleryEntity();
        $image->setImageUrl($imageUrl)->setProduct($this);
        $this->gallery->add($image);
        return $this;
    }
    
    /**
     * Get the product attributes
     * 
     * @return Collection Collection of attributes
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }
    
    /**
     * Get the product category
     * 
     * @return CategoryEntity|null The product category
     */
    public function getCategory(): ?CategoryEntity
    {
        return $this->category;
    }
    
    /**
     * Set the product category
     * 
     * @param CategoryEntity|null $category The new product category
     * @return void
     */
    public function setCategory(?CategoryEntity $category): void
    {
        $this->category = $category;
    }
}
