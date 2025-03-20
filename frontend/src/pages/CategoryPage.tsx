import React from 'react';
import { useAllProducts } from '@hooks/useProduct';
import ProductCard from '@components/ProductCard';
import Loader from "@components/UI/Loader";
import { ApolloError } from "@apollo/client";
import { Product } from "@/types/Product";

interface CategoryPageProps {
    category: string;
    categoryId?: number;
    title?: string;
}

const CategoryPage: React.FC<CategoryPageProps> = ({category, categoryId, title}) => {
    let loading: boolean;
    let error: ApolloError | undefined;
    let products: Product[];
    if (category?.toLowerCase().trim() === "all") {
        ({loading, error, products} = useAllProducts());
    } else {
        ({loading, error, products} = useAllProducts(categoryId));
    }

    // Display title with first letter capitalized if not provided explicitly
    const displayTitle = title || category.charAt(0).toUpperCase() + category.slice(1).toLowerCase();

    if (loading) return <Loader/>;
    if (error) return <div className="p-4 text-center text-red-500">Error loading products: {error.message}</div>;

    return (
        <div>
            <h1 className="text-4xl text-[#1D1F22] font-normal mb-24">{displayTitle}</h1>
            {products.length === 0 ? (
                <div className="text-center py-8">No products found in this category.</div>
            ) : (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    {products.map(product => (
                        <ProductCard key={product.id} product={product}/>
                    ))}
                </div>
            )}
        </div>
    );
};

export default CategoryPage;