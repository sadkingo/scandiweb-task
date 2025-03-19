import React from 'react';

interface QuantityProps {
    quantity: number;
    onIncrease: () => void;
    onDecrease: () => void;
    min?: number;
    max?: number;
}

export const Quantity: React.FC<QuantityProps> = (
    {
        quantity,
        onIncrease,
        onDecrease,
        min = 1,
        max = 99,
    }) => {
    return (
        <div className="flex items-center">
            <button
                type="button"
                onClick={onDecrease}
                disabled={quantity <= min}
                className="w-8 h-8 border border-black flex items-center justify-center font-medium disabled:opacity-50 disabled:cursor-not-allowed"
            >
                -
            </button>
            <span className="mx-3 font-medium">{quantity}</span>
            <button
                type="button"
                onClick={onIncrease}
                disabled={quantity >= max}
                className="w-8 h-8 border border-black flex items-center justify-center font-medium disabled:opacity-50 disabled:cursor-not-allowed"
            >
                +
            </button>
        </div>
    );
};

export default Quantity;