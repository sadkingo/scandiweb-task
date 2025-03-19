import { gql } from '@apollo/client';

// Mutation to create a new order
export const CREATE_ORDER = gql`
    mutation CreateOrder($currency_id: Int!, $products: [ProductInput!]!) {
        createOrder(
            currency_id: $currency_id,
            products: $products
        ) {
            id
            total
            currency {
                symbol
                label
            }
        }
    }
`;