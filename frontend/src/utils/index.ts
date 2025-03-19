/**
 * Utility functions for the application
 * These are pure functions that can be used across the application
 */

import { STORAGE_KEYS } from '@/constants';

/**
 * Persist data to localStorage
 * @param key - Storage key
 * @param data - Data to store
 */
export const saveToLocalStorage = <T>(key: string, data: T): void => {
  try {
    localStorage.setItem(key, JSON.stringify(data));
  } catch (error) {
    console.error(`Error saving to localStorage: ${error}`);
  }
};

/**
 * Save cart items to localStorage
 * @param cartItems - Cart items to save
 */
export const saveCartItems = <T>(cartItems: T[]): void => {
  saveToLocalStorage(STORAGE_KEYS.CART_ITEMS, cartItems);
};

/**
 * Get cart items from localStorage
 * @returns Cart items or empty array
 */
export function getCartItems<T>(): T[] {
  try {
    const items = localStorage.getItem(STORAGE_KEYS.CART_ITEMS);
    return items ? JSON.parse(items) : [];
  } catch (error) {
    console.error('Error getting cart items from localStorage:', error);
    return [];
  }
}