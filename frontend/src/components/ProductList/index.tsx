import React from 'react';
import { ProductCard } from '@components/ProductCard';
import { Product } from '@/types';

interface ProductListProps {
  products: Product[];
  title: string;
}

export const ProductList: React.FC<ProductListProps> = ({products, title}) => {
  
  return (
    <div className="py-12">
      <div className="container mx-auto px-4">
        {renderTitle()}
        {renderCards()}
      </div>
    </div>
  );

  function renderTitle() {
    return <h1 className="text-4xl font-normal mb-12">{title}</h1>;
  }

  function renderCards() {
    return <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-16">
      {products.map((product) => (
        <ProductCard key={product.id} product={product}/>
      ))}
    </div>;
  }
};

export default ProductList;