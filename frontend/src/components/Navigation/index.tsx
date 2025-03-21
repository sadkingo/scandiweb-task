import React, { useState, useRef, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Cart } from '@components/Cart';
import { useCartStore } from '@store/cartStore';
import NavLink from '@components/UI/NavLink';
import CartIcon from '@components/UI/CartIcon';
import { useCategories } from '@hooks/useCategories';
import logo from '@images/logo.png';

export const Navigation: React.FC = () => {
    const [showCart, setShowCart] = useState(false);
    const {clearLastAddedItem, totalItems, lastAddedItem} = useCartStore(state => state);
    const cartRef = useRef<HTMLDivElement>(null);
    const {categories, loading: categoriesLoading} = useCategories();

    // Close cart when clicking outside
    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (cartRef.current && !cartRef.current.contains(event.target as Node) && showCart) {
                setShowCart(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, [showCart]);

    //show cart when add item
    useEffect(() => {
        if (lastAddedItem) {
            setShowCart(true);
            clearLastAddedItem();
        }
    }, [lastAddedItem]);

    return (
        <>
            {showCart && (
                <div className="fixed inset-0 bg-[#393748]/22 bg-opacity-50 z-40"
                     onClick={() => setShowCart(false)}></div>
            )}
            <nav className="flex fixed justify-center h-16 border-b border-gray-200 bg-white w-full z-50">
                <div className="flex w-full justify-between items-center max-w-6xl mx-auto px-4">
                    {renderCategories()}
                    {renderLogo()}
                    {renderCartButton()}
                </div>
            </nav>
        </>
    );

    function renderLogo() {
        return <div className="max-md:hidden absolute left-1/2 transform -translate-x-1/2">
            <Link to="/">
                <img src={logo} alt="logo"></img>
            </Link>
        </div>;
    }

    function renderCartButton() {
        return <div className="flex items-center relative" ref={cartRef}>
            <button
                className="relative hover:opacity-80 transition-opacity"
                onClick={() => setShowCart(!showCart)}
                data-testid="cart-btn"
            >
                <CartIcon itemCount={totalItems}/>
            </button>

            {showCart && (
                <div id="cart-dropdown">
                    <Cart onClose={() => setShowCart(false)}/>
                </div>
            )}
        </div>;
    }

    function renderCategories() {
        return <div className="flex items-center h-full gap-4">
            {categoriesLoading ? (
                <div className="text-sm text-gray-500">Loading...</div>
            ) : (
                categories.map(category => (
                    <NavLink
                        key={category.id}
                        to={`/${category.name.toLowerCase()}`}
                        label={category.name.toUpperCase()}
                    />
                ))
            )}
        </div>;
    }
};

export default Navigation;