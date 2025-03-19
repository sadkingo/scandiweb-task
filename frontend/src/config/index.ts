/**
 * Application configuration
 * Centralizes all configuration settings for better maintainability
 */

// API configuration
export const API_CONFIG = {
    GRAPHQL_ENDPOINT: '/graphql/',
};

// Cache configuration
export const CACHE_CONFIG = {
    // Default cache policies
    DEFAULT_CACHE_POLICY: 'cache-first',
    PRODUCT_LIST_CACHE_POLICY: 'cache-and-network',
    PRODUCT_DETAIL_CACHE_POLICY: 'cache-first',

    // Cache expiration times (in milliseconds)
    PRODUCT_LIST_EXPIRATION: 5 * 60 * 1000, // 5 minutes
    PRODUCT_DETAIL_EXPIRATION: 10 * 60 * 1000, // 10 minutes
};

// Error messages
export const ERROR_MESSAGES = {
    NETWORK_ERROR: 'Network error: Please check your internet connection',
    PRODUCT_NOT_FOUND: 'Product not found',
    GENERAL_ERROR: 'Something went wrong. Please try again later.',
    ATTRIBUTE_SELECTION: 'Please select all required attributes',
};

// Route paths
export const ROUTES = {
    HOME: '/',
    PRODUCT: '/product/:id',
    CART: '/cart',
};