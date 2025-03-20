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