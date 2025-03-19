import React from 'react';

interface ColorSwatchProps {
  color: string;
  isSelected: boolean;
  onClick: () => void;
  title?: string;
}

export const ColorSwatch: React.FC<ColorSwatchProps> = (
  {
    color,
    isSelected,
    onClick,
    title,
  }) => {
  return (
    <button
      type="button"
      onClick={onClick}
      title={title}
      className={`w-8 h-8 rounded-sm ${isSelected ? 'ring-2 ring-offset-1 ring-black' : ''}`}
      style={{backgroundColor: color}}
    />
  );
};

export default ColorSwatch;