// Define all types used in the application

import { Price } from "@/types/graphql.ts";

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

export interface ProductAttribute {
    id: string;
    name: string;
    type: string;
    items: AttributeItem[];
}

export interface AttributeItem {
    id: string;
    displayValue: string;
    value: string;
}

export interface CartItem {
    id: string;
    name: string;
    price: number;
    quantity: number;
    size: string;
    color: string;
    imageUrl: string;
    selectedAttributes: {
        [key: string]: string;
    };
}

export interface Category {
    name: string;
    products: Product[];
}