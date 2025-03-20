import { Price } from "@/types/Price";
import { Category } from "@/types/Category";

export interface Product {
    id: string;
    name: string;
    brand: string;
    price: Price;
    category?: Category;
    description: string;
    imageUrl: string;
    gallery: string[];
    inStock: boolean;
    attributes: ProductAttribute[];
}

export interface AttributeItem {
    id: string;
    value: string;
    displayValue: string;
}

export interface ProductAttribute {
    id: string;
    name: string;
    type: string;
    items: AttributeItem[];
}

// Query response types
export interface GetProductResponse {
    product: Product;
}

export interface GetAllProductsResponse {
    products: Product[];
}
