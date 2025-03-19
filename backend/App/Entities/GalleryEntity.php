<?php

namespace App\Entities;

use Doctrine\ORM\Mapping;

/**
 * GalleryEntity class
 * 
 * Represents an image in a product's gallery.
 * Each gallery entity belongs to a product and contains an image URL.
 */
#[Mapping\Entity]
#[Mapping\Table(name: 'gallery')]
class GalleryEntity
{
    /**
     * @var int The unique identifier for the gallery item
     */
    #[Mapping\Id]
    #[Mapping\GeneratedValue]
    #[Mapping\Column(type: 'integer')]
    private int $id;

    /**
     * @var ProductEntity The product this gallery item belongs to
     */
    #[Mapping\ManyToOne(targetEntity: ProductEntity::class, inversedBy: 'gallery')]
    #[Mapping\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ProductEntity $product;

    /**
     * @var string The URL of the image
     */
    #[Mapping\Column(type: 'text')]
    private string $image_url;

    /**
     * Get the gallery item ID
     * 
     * @return int The gallery item ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the product this gallery item belongs to
     * 
     * @return ProductEntity The parent product
     */
    public function getProduct(): ProductEntity
    {
        return $this->product;
    }

    /**
     * Set the product this gallery item belongs to
     * 
     * @param ProductEntity $product The parent product
     * @return self
     */
    public function setProduct(ProductEntity $product): self
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get the image URL
     * 
     * @return string The image URL
     */
    public function getImageUrl(): string
    {
        return $this->image_url;
    }

    /**
     * Set the image URL
     * 
     * @param string $imageUrl The new image URL
     * @return self
     */
    public function setImageUrl(string $imageUrl): self
    {
        $this->image_url = $imageUrl;
        return $this;
    }
}
