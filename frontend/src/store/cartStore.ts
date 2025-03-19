import { create } from 'zustand';
import { persist, PersistOptions } from 'zustand/middleware';
import { STORAGE_KEYS } from '@/constants';
import { saveCartItems, getCartItems } from '@/utils';
import { Attribute } from "@/types/graphql.ts";

/**
 * Interface for cart item attributes
 */
export interface CartItemAttribute {
  id: string;
  name: string;
  value: string;
}

/**
 * Interface for cart items
 */
export interface CartItem {
  id: string;
  originalId: string;
  name: string;
  price: number;
  quantity: number;
  imageUrl?: string;
  selectedAttributes: Record<string, { id: string; name: string }>;
  attributes?: Attribute[];
}

/**
 * Interface for cart state
 */
export interface CartState {
  items: CartItem[];
  totalItems: number;
  totalPrice: number;
  lastAddedItem: CartItem | null;
  clearLastAddedItem: () => void;
  addItem: (item: CartItem) => void;
  removeItem: (itemId: string) => void;
  updateQuantity: (itemId: string, quantity: number) => void;
  clearCart: () => void;
  isItemInCart: (itemId: string) => boolean;
}

// Define the type for persisted state
type PersistedCartState = {
  items: CartItem[];
  totalItems: number;
  totalPrice: number;
};

// Define persist configuration
const persistConfig: PersistOptions<
  CartState,
  PersistedCartState
> = {
  name: STORAGE_KEYS.CART_ITEMS,
  partialize: (state) => ({
    items: state.items,
    totalItems: state.totalItems,
    totalPrice: state.totalPrice,
  }),
};

/**
 * Cart store using Zustand with persistence
 */
export const useCartStore = create<CartState>()(
  persist(
    (set, get) => ({
      items: (() => {
        try {
          return getCartItems<CartItem>() || [];
        } catch (error) {
          console.error('Error initializing cart items:', error);
          return [];
        }
      })(),
      totalItems: (() => {
        try {
          const items = getCartItems<CartItem>() || [];
          return items.length > 0
            ? items.reduce((total, item) => total + item.quantity, 0)
            : 0;
        } catch (error) {
          console.error('Error calculating total items:', error);
          return 0;
        }
      })(),
      totalPrice: (() => {
        try {
          const items = getCartItems<CartItem>() || [];
          return items.length > 0
            ? items.reduce((total, item) => total + (item.price * item.quantity), 0)
            : 0;
        } catch (error) {
          console.error('Error calculating total price:', error);
          return 0;
        }
      })(),
      lastAddedItem: null, // Track the last added item

      /**
       * Clear last added item
       */
      clearLastAddedItem: () => set({lastAddedItem: null}),

      /**
       * Add item to cart
       */
      addItem: (item: CartItem) => set((state) => {
        const existingItem = state.items.find((i) => i.id === item.id);

        let updatedItems;
        if (existingItem) {
          updatedItems = state.items.map((i) =>
            i.id === item.id ? {...i, quantity: i.quantity + item.quantity} : i,
          );
        } else {
          updatedItems = [...state.items, item];
        }

        const newTotalItems = state.totalItems + item.quantity;
        const newTotalPrice = state.totalPrice + item.price * item.quantity;

        // Save to localStorage
        saveCartItems(updatedItems);

        return {
          items: updatedItems,
          totalItems: newTotalItems,
          totalPrice: newTotalPrice,
          lastAddedItem: item,
        };
      }),

      /**
       * Remove item from cart
       */

      removeItem: (itemId: string) => set((state) => {
        const itemToRemove = state.items.find(i => i.id === itemId);
        if (!itemToRemove) return state;

        const updatedItems = state.items.filter(i => i.id !== itemId);
        const newTotalItems = state.totalItems - itemToRemove.quantity;
        const newTotalPrice = state.totalPrice - (itemToRemove.price * itemToRemove.quantity);

        // Save to localStorage
        saveCartItems(updatedItems);

        return {
          items: updatedItems,
          totalItems: newTotalItems,
          totalPrice: newTotalPrice,
        };
      }),

      /**
       * Update item quantity
       */
      updateQuantity: (itemId: string, quantity: number) => set((state) => {
        if (quantity < 1) return state;

        const item = state.items.find(i => i.id === itemId);
        if (!item) return state;

        const quantityDiff = quantity - item.quantity;
        const priceDiff = item.price * quantityDiff;

        const updatedItems = state.items.map(i =>
          i.id === itemId ? {...i, quantity} : i,
        );

        // Save to localStorage
        saveCartItems(updatedItems);

        return {
          items: updatedItems,
          totalItems: state.totalItems + quantityDiff,
          totalPrice: state.totalPrice + priceDiff,
        };
      }),

      /**
       * Clear cart
       */
      clearCart: () => {
        // Clear localStorage
        saveCartItems([]);
        return set({items: [], totalItems: 0, totalPrice: 0});
      },

      /**
       * Check if item is in cart
       */
      isItemInCart: (itemId: string) => {
        const state = get();
        return state.items.some(item => item.id === itemId);
      },
    }),
    persistConfig,
  ),
);
