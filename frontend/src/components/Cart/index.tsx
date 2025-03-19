import React, { useState } from 'react';
import { useMutation } from '@apollo/client';
import { CartItem, useCartStore } from '@store/cartStore';
import { CREATE_ORDER } from '@/graphql/mutations';
import { processApolloError } from '@/utils/errorHandling';
import { CartProduct } from "@components/CartProduct";

interface CartProps {
    onClose: () => void;
}

export const Cart: React.FC<CartProps> = ({onClose}) => {
    const {items, totalItems} = useCartStore();
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [createOrder, {loading}] = useMutation(CREATE_ORDER);

    const totalPrice = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    async function handleOrderCreate() {
        if (items.length > 0 && !isSubmitting) {
            try {
                setIsSubmitting(true);

                // Format cart items for the mutation
                const orderProducts = items.map(item => ({
                    id: item.originalId,
                    quantity: item.quantity,
                    // Convert selectedAttributes to the format expected by the API
                    selectedAttributes: JSON.stringify(
                        Object.entries(item.selectedAttributes).map(([_, attr]) => attr.id),
                    ),
                }));

                // Execute the createOrder mutation
                const {data} = await createOrder({
                    variables: {
                        currency_id: 1, // Using default currency ID of 1
                        products: orderProducts,
                    },
                });
                console.log('Order created:', data);
                useCartStore.getState().clearCart();
                onClose();
            } catch (err) {
                console.error('Error creating order:', err);
                const errorResponse = processApolloError(err as any);
                alert(`Failed to place order: ${errorResponse.message}`);
            } finally {
                setIsSubmitting(false);
            }
        }
    }

    return (
        <div className="absolute top-full right-0 mt-2 w-90 bg-white shadow-lg z-50 px-2 py-8">
            {renderCartHeader()}
            {renderProducts()}
            {renderCartFooter()}
        </div>
    );

    function renderCartHeader() {
        return <div className="flex justify-between items-center mb-4">
            <h3 className="font-medium">
                <span className="font-bold">My Bag, </span>
                {totalItems} {totalItems === 1 ? 'item' : 'items'}
            </h3>
            <button onClick={onClose} className="text-gray-500 text-xl font-bold cursor-pointer">×</button>
        </div>;
    }

    function renderProducts() {
        return <div
            className="h-80 overflow-y-auto px-2"
            data-testid="cart-overlay"
        >
            {items.map(renderProduct)}
        </div>;
    }

    function renderProduct(product: CartItem) {
        return <CartProduct key={product.id} product={product}/>
    }

    function renderCartFooter() {
        return <>
            <div className="mt-6 font-bold flex justify-between px-2 font-[Roboto]!">
                <span>Total:</span>
                <span data-testid="cart-item-amount">${totalPrice.toFixed(2)}</span>
            </div>

            <div className="mt-6 flex space-x-2 px-2">
                <button
                    className={`flex-1 text-white py-3 text-center text-sm font-medium ${items.length > 0 ? 'bg-[#5ECE7B] cursor-pointer hover:bg-green-600 transition-colors duration-200' : 'bg-gray-400 cursor-not-allowed'}`}
                    onClick={handleOrderCreate}
                    disabled={items.length === 0 || isSubmitting || loading}
                >
                    {isSubmitting || loading ? 'PROCESSING...' : 'PLACE ORDER'}
                </button>
            </div>
        </>;
    }

};

export default Cart;