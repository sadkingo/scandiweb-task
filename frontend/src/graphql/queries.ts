import { gql } from '@apollo/client';

// Query to fetch all categories
export const GET_CATEGORIES = gql`
    query {
        categories {
            id
            name
        }
    }
`;

// Query to fetch a single product by ID
export const GET_PRODUCT = gql`
    query GetProduct($id: String!) {
        product(id: $id) {
            id
            name
            description
            brand
            inStock
            gallery
            price {
                amount
                currency {
                    label
                    symbol
                }
            }
            attributes {
                id
                name
                type
            }
        }
    }
`;

// Original query with attribute items
export const GET_PRODUCT_WITH_ITEMS = gql`
    query GetProductWithItems($id: String!) {
        product(id: $id) {
            id
            name
            description
            brand
            inStock
            gallery
            price {
                amount
                currency {
                    label
                    symbol
                }
            }
            attributes {
                name
                type
                items {
                    id
                    value
                    displayValue
                }
            }
        }
    }
`;

// Query to fetch all products
export const GET_ALL_PRODUCTS = gql`
    query GetProducts($categoryId: Int) {
        products(categoryId: $categoryId) {
            id
            name
            brand
            inStock
            gallery
            category {
                name
            }
            price {
                amount
                currency {
                    label
                    symbol
                }
            }
            attributes {
                name
                type
                items {
                    id
                    value
                    displayValue
                }
            }
        }
    }
`;