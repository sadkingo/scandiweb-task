import React, { useState } from 'react';
import { useMutation } from '@apollo/client';
import { useCartStore } from '@store/cartStore';
import { CREATE_ORDER } from '@/graphql/mutations';
import { processApolloError } from '@/utils/errorHandling';

interface CartProps {
  onClose: () => void;
}

export const Cart: React.FC<CartProps> = ({onClose}) => {
  const {items, removeItem, updateQuantity, totalItems} = useCartStore();
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
      {items.map(product => (
        <div key={product.id} className="flex py-5">
          <div className="flex-grow max-w-[150px]">
            <p className="font-medium">{product.name}</p>
            <p className="text-sm font-bold mt-1">${product.price.toFixed(2)}</p>

            {/* Render each attribute */}
            {product.attributes?.map(attribute => (
              <div key={attribute.name}
                   className="mb-6"
                   data-testid={`cart-item-attribute-${attribute.name}`}
              >
                <h3 className="font-medium text-[14px] mb-2">
                  {attribute.name}:
                </h3>
                <div className="flex flex-wrap gap-2">
                  {attribute.items?.map(item => {
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
                  })}
                </div>
              </div>
            ))}
          </div>

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

          <div className="ml-2 flex-1 bg-gray-100 flex-shrink-0">
            {product.imageUrl ? (
              <img
                src={product.imageUrl}
                alt={product.name}
                className="h-full object-contain"
              />
            ) : (
              <div className="w-full h-full flex items-center justify-center text-gray-400 text-xs text-center">
                No image available
              </div>
            )}
          </div>
        </div>
      ))}
    </div>;
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