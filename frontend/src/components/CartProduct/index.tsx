import React from "react";
import { CartItem, useCartStore } from "@store/cartStore.ts";
import { Attribute, AttributeItem } from "@/types/graphql";

interface CartProductProps {
    product: CartItem,
}

export const CartProduct: React.FC<CartProductProps> = ({product}) => {
    const {removeItem, updateQuantity} = useCartStore();

    return (
        <div key={product.id} className="flex py-5">
            {renderProductAttributes()}
            {renderProductQuantityControls()}
            {renderProductImage()}
        </div>
    )

    function renderProductAttributes() {
        return (
            <div className="flex-grow max-w-[150px]">
                <p className="font-medium">{product.name}</p>
                <p className="text-sm font-bold mt-1">${product.price.toFixed(2)}</p>

                {/* Render each attribute */}
                {product.attributes?.map(renderAttributes)}
            </div>
        )
    }

    function renderAttributes(attribute: Attribute) {
        return (
            <div key={attribute.name}
                 className="mb-6"
                 data-testid={`cart-item-attribute-${attribute.name}`}
            >
                <h3 className="font-medium text-[14px] mb-2">
                    {attribute.name}:
                </h3>
                <div className="flex flex-wrap gap-2">
                    {attribute.items?.map((item) => renderAttributeItems(attribute, item))}
                </div>
            </div>
        )
    }

    function renderAttributeItems(attribute: Attribute, item: AttributeItem) {
        // Render color swatches for color attributes
        const isSelected = product.selectedAttributes[attribute.name].id === item.id;
        if (attribute.type === 'swatch' || attribute.name.toLowerCase().includes('color')) {
            return (
                <button
                    key={item.value}
                    className={`w-6 h-6 ${isSelected ? 'ring-2 ring-offset-2 ring-green-500' : ''}`}
                    style={{backgroundColor: item.value}}
                    title={item.displayValue}
                    data-testid={`cart-item-attribute-${attribute.name}-${item.displayValue}` + (isSelected ? '-selected' : '')}
                />
            );
        }

        // Render text buttons for other attributes
        return (
            <button
                key={item.value}
                className={`min-w-7 h-7 p-1 text-[14px] flex items-center justify-center border ${isSelected ? 'bg-black text-white border border-black' : 'hover:bg-gray-100'}`}
                data-testid={`cart-item-attribute-${attribute.name}-${item.displayValue}` + (isSelected ? '-selected' : '')}
            >
                {item.value}
            </button>
        );
    }

    function renderProductQuantityControls() {
        return (
            <div className="flex flex-col justify-between items-center ml-2">
                <div className="flex flex-col items-center h-full justify-between">
                    <button
                        className="w-6 h-6 text-xl flex border cursor-pointer items-center justify-center font-medium"
                        onClick={() => updateQuantity(product.id, product.quantity + 1)}
                        data-testid="cart-item-amount-increase"
                    >
                        +
                    </button>
                    <span className="my-1 text-sm">{product.quantity}</span>
                    <button
                        className="w-6 h-6 flex text-xl border cursor-pointer items-center justify-center font-medium"
                        onClick={() => {
                            if (product.quantity === 1) {
                                removeItem(product.id);
                            } else {
                                updateQuantity(product.id, product.quantity - 1);
                            }
                        }}
                        data-testid="cart-item-amount-decrease"
                    >
                        -
                    </button>
                </div>
            </div>
        )
    }

    function renderProductImage() {
        return (
            <div className="ml-2 flex-1 bg-gray-100 flex-shrink-0">
                {product.imageUrl ? (
                    <img
                        src={product.imageUrl}
                        alt={product.name}
                        className="h-full object-contain"
                    />
                ) : (
                    <div
                        className="w-full h-full flex items-center justify-center text-gray-400 text-xs text-center">
                        No image available
                    </div>
                )}
            </div>
        );
    }
}