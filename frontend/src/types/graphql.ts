// Types for GraphQL responses

// Currency type
export interface Currency {
  label: string;
  symbol: string;
}

// Price type
export interface Price {
  amount: string;
  currency: Currency;
}

// Attribute item type
export interface AttributeItem {
  id: string;
  value: string;
  displayValue: string;
}

// Attribute type
export interface Attribute {
  name: string;
  type: string;
  items?: AttributeItem[];
}

// Category type
export interface Category {
  name: string;
}

// Product type
export interface Product {
  id: string;
  name: string;
  brand: string;
  description: string;
  inStock: boolean;
  gallery: string[];
  price: Price;
  attributes: Attribute[];
  category?: Category;
}

// Query response types
export interface GetProductResponse {
  product: Product;
}

export interface GetAllProductsResponse {
  products: Product[];
}