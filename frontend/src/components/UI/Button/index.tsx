import React from 'react';

interface ButtonProps {
    children: React.ReactNode;
    onClick?: () => void;
    variant?: 'primary' | 'secondary' | 'outline';
    fullWidth?: boolean;
    disabled?: boolean;
    type?: 'button' | 'submit' | 'reset';
    className?: string;
}

export const Button: React.FC<ButtonProps> = (
    {
        children,
        onClick,
        variant = 'primary',
        fullWidth = false,
        disabled = false,
        type = 'button',
        className = '',
    }) => {
    const baseClasses = 'py-3 px-6 font-medium transition-colors duration-200';

    const variantClasses = {
        primary: 'bg-green-500 text-white hover:bg-green-600',
        secondary: 'bg-black text-white hover:bg-gray-800',
        outline: 'border border-black text-black hover:bg-gray-100',
    };

    const widthClass = fullWidth ? 'w-full' : '';

    return (
        <button
            type={type}
            onClick={onClick}
            disabled={disabled}
            className={`${baseClasses} ${variantClasses[variant]} ${widthClass} ${disabled ? 'opacity-50 cursor-not-allowed' : ''} ${className}`}
        >
            {children}
        </button>
    );
};

export default Button;