import React from 'react';

interface SizeSelectorProps {
  sizes: { id: string; value: string; displayValue: string }[];
  selectedSize: string;
  onSelect: (size: string) => void;
}

export const SizeSelector: React.FC<SizeSelectorProps> = ({
  sizes,
  selectedSize,
  onSelect,
}) => {
  return (
    <div className="flex flex-wrap gap-2">
      {sizes.map((size) => (
        <button
          key={size.id}
          type="button"
          onClick={() => onSelect(size.value)}
          className={`min-w-10 h-10 px-3 border text-sm font-medium ${selectedSize === size.value ? 'bg-black text-white' : 'border-gray-300 hover:border-gray-500'}`}
        >
          {size.displayValue}
        </button>
      ))}
    </div>
  );
};

export default SizeSelector;