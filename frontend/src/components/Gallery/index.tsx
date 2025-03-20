import Arrow from "@images/arrow.png";
import React, { useState } from "react";
import { Product } from "@/types/Product";

interface GalleryProps {
    product: Product
}

const Gallery: React.FC<GalleryProps> = ({product}) => {
    const [mainImage, setMainImage] = useState(0);
    
    return <div className="flex" data-testid="product-gallery">
        <div className="hidden md:flex flex-col space-y-4 mr-4">
            {product!.gallery.map((img, index) => (
                <button
                    key={index}
                    className={`w-16 h-16 ${mainImage === index ? 'border-2 border-black' : ''}`}
                    onClick={() => setMainImage(index)}
                >
                    <img
                        src={img}
                        alt={`${product!.name} - ${index + 1}`}
                        className="w-full h-full object-cover cursor-pointer"
                    />
                </button>
            ))}
        </div>

        <div className="flex-grow">
            <div className="relative aspect-square mb-4 border">
                <img
                    src={Arrow}
                    alt={product!.name}
                    className="w-8 h-8 absolute -translate-y-1/2 top-1/2 ms-4 rotate-180 cursor-pointer"
                    onClick={() =>
                        setMainImage((index) =>
                            (index - 1 + product!.gallery.length) % product!.gallery.length,
                        )
                    }
                />
                <img
                    src={Arrow}
                    alt={product!.name}
                    className="w-8 h-8 absolute -translate-y-1/2 right-0 top-1/2 me-4 cursor-pointer"
                    onClick={
                        () => setMainImage(
                            (index) => (index + 1) % product!.gallery.length,
                        )
                    }
                />
                {product!.gallery.length > 0 ? (
                    <img
                        src={product!.gallery[mainImage]}
                        alt={product!.name}
                        className="w-full h-full object-contain"
                    />
                ) : (
                    <div className="w-full h-full flex items-center justify-center">
                        <span className="text-gray-400">No image available</span>
                    </div>
                )}
            </div>
        </div>
    </div>;
}

export default Gallery;