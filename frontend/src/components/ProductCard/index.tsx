import React from 'react';
import { Link } from 'react-router-dom';
import { Product } from '@/types/Product';
import { useCartStore } from '@store/cartStore';

interface ProductCardProps {
    product: Product;
}

export const ProductCard: React.FC<ProductCardProps> = ({product}) => {
    const {addItem} = useCartStore();

    const handleAddToCart = (e: React.MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();

        // Since the new query doesn't include attribute items, we'll use default values
        const selectedAttributes = product.attributes.reduce((acc, attr) => {
            // Set default values based on attribute name
            acc[attr.name] = {id: attr.items![0].id, name: attr.items![0].value};
            return acc;
        }, {} as Record<string, { id: string, name: string }>);

        // Create a unique ID based on product ID and selected attributes
        const uniqueId = `${product.id}-${Object.values(selectedAttributes).map(e => e.id).join('-')}`;

        addItem({
            id: uniqueId,
            originalId: product.id,
            name: product.name,
            attributes: product.attributes,
            price: parseFloat(product.price.amount),
            quantity: 1,
            imageUrl: product.gallery[0],
            selectedAttributes,
        });
    };

    return (
        <div className="group relative p-4 hover:shadow-[0px_4px_35px_rgba(168,172,176,0.19)]">
            <Link data-testid={`product-${product.name.toLowerCase().split(" ").join("-")}`}
                  to={`/product/${product.id}`}
                  className="block">
                {renderImage()}
                {renderTitle()}
                {renderPrice()}
            </Link>
        </div>
    );

    function renderImage() {
        return <div className="relative aspect-square overflow-hidden bg-gray-100 mb-4">
            <img
                src={product.gallery[0]}
                alt={product.name}
                className={`w-full h-full object-cover transition-transform duration-300 group-hover:scale-105 ${!product.inStock && "opacity-40"}`}
            />
            {!product.inStock && (
                <div className="absolute inset-0  flex items-center justify-center">
                    <p className="text-xl text-[#8D8F9A] font-medium">OUT OF STOCK</p>
                </div>
            )}
            {product.inStock && renderBuyButton()}
        </div>;
    }

    function renderTitle() {
        return <h3 className="text-base font-light text-gray-900">{product.name}</h3>;
    }

    function renderPrice() {
        return <p
            className="mt-1 text-lg font-normal">{product.price.currency.symbol}{parseFloat(product.price.amount).toFixed(2)}
        </p>;
    }

    function renderBuyButton() {
        return <button
            className="absolute bottom-4 right-4 w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-lg hover:cursor-pointer transform active:scale-75 transition-transform"
            onClick={handleAddToCart}
        >
            <svg className="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z"/>
            </svg>
        </button>;
    }
};

export default ProductCard;