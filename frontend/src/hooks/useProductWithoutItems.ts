import { useQuery } from '@apollo/client';
import { GET_PRODUCT } from '../graphql/queries';
import { GetProductResponse, Product } from '../types/graphql';

// Hook for fetching a single product by ID using the query without attribute items
export const useProductWithoutItems = (id: string) => {
  const { loading, error, data } = useQuery<GetProductResponse>(GET_PRODUCT, {
    variables: { id },
    skip: !id,
  });

  // If the product is loaded, we need to add default values for attribute items
  // since our query doesn't include them
  const processedProduct = data?.product ? {
    ...data.product,
    attributes: data.product.attributes.map(attr => ({
      ...attr,
      // Add empty items array to maintain compatibility with components expecting items
      items: []
    }))
  } : undefined;

  return {
    loading,
    error,
    product: processedProduct as Product | undefined,
  };
};