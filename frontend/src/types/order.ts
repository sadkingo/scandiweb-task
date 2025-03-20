/**
 * Input type for order product items
 */
export interface OrderProductInput {
    id: string;          // Product ID
    quantity: number;    // Quantity of the product
    selectedAttributes: string;  // JSON string of selected attribute IDs
}

/**
 * Order product item in response
 */
export interface OrderedProduct {
    product_id: string;  // Product ID
    quantity: number;    // Quantity ordered
    unit_price: number;  // Unit price
    total: number;       // Total price for this product
    selected_attributes: string; // JSON string of selected attribute IDs
}

/**
 * Order response type
 */
export interface Order {
    id: string;          // Order ID
    total: number;       // Total order amount
    currency: {
        symbol: string;    // Currency symbol
        label: string;     // Currency label
    };
    orderedProducts: OrderedProduct[]; // Products in the order
}

/**
 * Create order response
 */
export interface CreateOrderResponse {
    createOrder: Order;
}