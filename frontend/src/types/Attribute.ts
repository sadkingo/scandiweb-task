export interface Attribute {
    name: string;
    type: string;
    items?: AttributeItem[];
}

export interface AttributeItem {
    id: string;
    displayValue: string;
    value: string;
}