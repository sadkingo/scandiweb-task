import { useQuery } from '@apollo/client';
import { useMemo } from 'react';
import { GET_CATEGORIES } from '../graphql/queries';

/**
 * Type definition for the categories response
 */
interface CategoriesResponse {
  categories: {
    id: string;
    name: string;
  }[];
}

/**
 * Custom hook for fetching all available categories
 * 
 * @returns Object containing loading state, error, and categories data
 */
export const useCategories = () => {
  const { loading, error, data } = useQuery<CategoriesResponse>(GET_CATEGORIES, {
    fetchPolicy: 'cache-first', // Optimize for performance with caching
    errorPolicy: 'all', // Handle both GraphQL and network errors
  });

  // Memoize the categories data to prevent unnecessary re-renders
  const categories = useMemo(() => {
    if (!data?.categories) return [];
    return data.categories.map(category => ({
      id: category.id,
      name: category.name
    }));
  }, [data?.categories]);

  return {
    loading,
    error,
    categories,
    hasError: !!error,
    isEmpty: !loading && !error && categories.length === 0,
  };
};

export default useCategories;