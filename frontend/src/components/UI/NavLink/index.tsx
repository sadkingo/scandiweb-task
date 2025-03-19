import React from 'react';
import { Link, useLocation } from 'react-router-dom';

interface NavLinkProps {
    to: string;
    label: string;
    exactPath?: boolean;
    className?: string;
}

/**
 * NavLink component for navigation items
 * Handles active state styling based on current route
 */
export const NavLink: React.FC<NavLinkProps> = (
    {
        to,
        label,
        exactPath = false,
        className = '',
    }) => {
    const location = useLocation();

    // Determine if this link is active
    const isActive = exactPath
        ? location.pathname === to
        : location.pathname === to || (to !== '/' && location.pathname.startsWith(to));

    return (
        <Link
            to={to}
            className={`${className} flex flex-col justify-center items-center relative h-full ${isActive && 'text-[#5ECE7B] font-semibold'}`}
            data-testid={isActive ? 'active-category-link' : 'category-link'}
        >
            {label}
            <span className={`absolute bottom-0 ${isActive && 'border-b-2 border-[#5ECE7B]'}`}
                  style={{width: 'calc(100% + 16px)'}}
            />
        </Link>
    );
};

export default NavLink;