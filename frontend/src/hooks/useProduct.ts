import { useQuery, ApolloError } from '@apollo/client';
import { useMemo, useCallback } from 'react';
import { GET_PRODUCT, GET_PRODUCT_WITH_ITEMS, GET_ALL_PRODUCTS } from '../graphql/queries';
import { GetProductResponse, GetAllProductsResponse } from '@/types/Product';

/**
 * Custom hook for fetching a single product by ID
 *
 * @param id - The product ID to fetch
 * @param includeAttributeItems - Whether to include attribute items in the query
 * @returns Object containing loading state, error, and product data
 */
export const useProduct = (id: string, includeAttributeItems: boolean = false) => {
    // Select the appropriate query based on whether attribute items are needed
    const QUERY = includeAttributeItems ? GET_PRODUCT_WITH_ITEMS : GET_PRODUCT;

    const {loading, error, data, refetch} = useQuery<GetProductResponse>(QUERY, {
        variables: {id},
        skip: !id,
        fetchPolicy: 'cache-first',
        errorPolicy: 'all',
    });

    // Memoize the product data to prevent unnecessary re-renders
    const product = useMemo(() => data?.product, [data]);

    // Provide a refetch callback that's stable across renders
    const refreshProduct = useCallback(() => {
        if (id) {
            return refetch({id});
        }
        return Promise.reject(new Error('Cannot refresh: No product ID provided'));
    }, [id, refetch]);

    return {
        loading,
        error,
        product,
        refreshProduct,
        hasError: !!error,
        isEmpty: !loading && !error && !product,
    };
};

/**
 * Custom hook for fetching all products with optional category filtering
 *
 * @param categoryId - Optional category ID to filter products by
 * @returns Object containing loading state, error, and filtered products
 */
export const useAllProducts = (categoryId?: number) => {
    const {loading, error, data, refetch} = useQuery<GetAllProductsResponse>(GET_ALL_PRODUCTS, {
        variables: categoryId ? {categoryId: categoryId} : {},
        fetchPolicy: 'cache-and-network', // Get cached data fast, but still update from network
        errorPolicy: 'all',
    });

    // Memoize products to prevent unnecessary re-renders
    const products = useMemo(() => {
        if (!data?.products) return [];
        return data.products;
    }, [data?.products]);


    // Provide a stable refetch callback
    const refreshProducts = useCallback(() => refetch(), [refetch]);

    return {
        loading,
        error,
        products,
        refreshProducts,
        hasError: !!error,
        isEmpty: !loading && !error && products.length === 0,
        totalCount: products.length,
    };
};

/**
 * Helper function to handle Apollo errors consistently
 *
 * @param error - The Apollo error object
 * @returns A user-friendly error message
 */
export const getErrorMessage = (error?: ApolloError): string => {
    if (!error) return '';

    // Handle network errors
    if (error.networkError) {
        return 'Network error: Please check your internet connection';
    }

    // Handle GraphQL errors
    if (error.graphQLErrors?.length) {
        return `GraphQL error: ${error.graphQLErrors[0].message}`;
    }

    // Fallback for other errors
    return error.message || 'An unknown error occurred';
};