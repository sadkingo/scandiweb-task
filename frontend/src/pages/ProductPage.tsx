import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import parse from "html-react-parser";
import DOMPurify from 'dompurify';
import { useCartStore } from '@store/cartStore';
import { useProduct } from '@hooks/useProduct';
import PageLoader from "@components/UI/PageLoader";
import Arrow from '/public/images/arrow.png';

const ProductPage: React.FC = () => {
  const {id} = useParams<{ id: string }>();
  const addItem = useCartStore(state => state.addItem);
  const [isAllAttributesSelected, setIsAllAttributesSelected] = useState(false);
  const {loading, error, product} = useProduct(id || '', true);

  // State for showing full description
  const [showFullDescription, setShowFullDescription] = useState(false);

  // State for selected attributes
  const [selectedAttributes, setSelectedAttributes] = useState<Record<string, { id: string, name: string }>>({});
  const [mainImage, setMainImage] = useState(0);

  // Initialize selected attributes when product data loads
  useEffect(() => {
    if (product) {
      const initialAttributes: Record<string, { id: string, name: string }> = {};
      product.attributes.forEach(attr => {
        if (attr.items && attr.items.length > 0) {
          initialAttributes[attr.name] = {id: "", name: ""};
        }
      });
      setSelectedAttributes(initialAttributes);
      if (Object.keys(initialAttributes).length === 0)
        setIsAllAttributesSelected(true);
    }
  }, [product]);

  // Handle Description show more
  function handleDescriptionShowMore() {
    return showFullDescription
      ? product!.description
      : (product!.description?.slice(0, 500).trim()
      + (product!.description.length > 500 ? "..." : "")) || 'No description available.'
  }

  // Handle attribute selection
  const handleAttributeChange = (attributeName: string, value: string, id: string) => {
    setSelectedAttributes(prev => ({
      ...prev,
      [attributeName]: {id, name: value},
    }));

    if (!product) return;
    // Check if all required attributes are selected
    const isAllSelected = product.attributes.every(attr => {
        if (!attr.items?.length) return true;
        if (attr.name === attributeName) return true;
        return selectedAttributes[attr.name].name;
      },
    );
    setIsAllAttributesSelected(isAllSelected);
  };

  const handleAddToCart = () => {
    if (!product) return;
    // Create a unique ID based on product ID and selected attributes
    const uniqueId = `${product.id}-${Object.values(selectedAttributes).map(e => e.id).join('-')}`;

    addItem({
      id: uniqueId,
      originalId: product.id,
      name: product.name,
      price: parseFloat(product.price.amount),
      quantity: 1,
      attributes: product.attributes,
      selectedAttributes: {...selectedAttributes},
      imageUrl: product.gallery[0],
    });
  };

  // Render loading and error states
  if (loading) return <PageLoader/>;
  if (error) return <div className="p-4 text-center text-red-500">Error loading product: {error.message}</div>;
  if (!product) return <div className="p-4 text-center">Product not found</div>;

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
      {renderGallery()}
      {renderSubmissionForm()}
    </div>
  );

  function renderSubmissionForm() {
    return <div>
      <h2 className="text-xl font-medium text-gray-500 mb-1">{product!.brand}</h2>
      <h1 className="text-3xl font-medium mb-1">{product!.name}</h1>
      <p className="text-xl font-bold mb-8">
        {product!.price.currency.symbol}{parseFloat(product!.price.amount).toFixed(2)}
      </p>

      {/* Render each attribute */}
      {product!.attributes.map(attribute => (
        <div
          key={attribute.name}
          className="mb-6"
          data-testid={`product-attribute-${attribute.name.toLowerCase()}`}
        >
          <h3 className="font-medium uppercase mb-2">{attribute.name}:</h3>
          <div className="flex flex-wrap gap-2">
            {attribute.items?.map(item => {
              const isSelected = selectedAttributes[attribute.name]?.name === item.value;

              // Render color swatches for color attributes
              if (attribute.type === 'swatch' || attribute.name.toLowerCase().includes('color')) {
                return (
                  <button
                    key={item.value}
                    className={`w-8 h-8 cursor-pointer ${isSelected ? 'ring-2 ring-offset-2 ring-green-500' : ''}`}
                    style={{backgroundColor: item.value}}
                    onClick={() => handleAttributeChange(attribute.name, item.value, item.id)}
                    data-testid={`product-attribute-${attribute.name.toLowerCase()}-${item.displayValue}`}
                    title={item.displayValue}
                  />
                );
              }

              // Render text buttons for other attributes
              return (
                <button
                  key={item.value}
                  className={`min-w-10 h-10 px-3 flex items-center justify-center border cursor-pointer ${isSelected ? 'bg-black text-white' : 'hover:bg-gray-100'}`}
                  onClick={() => handleAttributeChange(attribute.name, item.value, item.id)}
                  data-testid={`product-attribute-${attribute.name.toLowerCase()}-${item.displayValue}`}
                >
                  {item.displayValue}
                </button>
              );
            })}
          </div>
        </div>
      ))}

      <button
        className={`w-full py-3 font-medium ${(product!.inStock && isAllAttributesSelected) ? 'bg-green-500 text-white cursor-pointer' : 'bg-gray-300 text-gray-500 cursor-not-allowed'}`}
        onClick={handleAddToCart}
        disabled={!product!.inStock || !isAllAttributesSelected}
        data-testid="add-to-cart"
      >
        {product!.inStock ? 'ADD TO CART' : 'OUT OF STOCK'}
      </button>

      <div className="mt-8">
        <div className="text-wrap w-full truncate" data-testid="product-description">
          {parse(DOMPurify.sanitize(handleDescriptionShowMore()))}
        </div>

        {product!.description?.length > 500 && (
          <button
            className="mt-2 w-full text-sm text-green-500 hover:text-gray-700"
            onClick={() => setShowFullDescription(prev => !prev)}
          >
            {showFullDescription ? 'Show Less' : 'Show More'}
          </button>
        )}
      </div>
    </div>;
  }

  function renderGallery() {
    return <div className="flex" data-testid="product-gallery">
      <div className="hidden md:flex flex-col space-y-4 mr-4">
        {product!.gallery.map((img, index) => (
          <button
            key={index}
            className={`w-16 h-16 ${mainImage === index ? 'border-2 border-black' : ''}`}
            onClick={() => setMainImage(index)}
          >
            <img
              src={img}
              alt={`${product!.name} - ${index + 1}`}
              className="w-full h-full object-cover cursor-pointer"
            />
          </button>
        ))}
      </div>

      <div className="flex-grow">
        <div className="relative aspect-square mb-4 border">
          <img
            src={Arrow}
            alt={product!.name}
            className="w-8 h-8 absolute -translate-y-1/2 top-1/2 ms-4 rotate-180 cursor-pointer"
            onClick={() =>
              setMainImage((index) =>
                (index - 1 + product!.gallery.length) % product!.gallery.length,
              )
            }
          />
          <img
            src={Arrow}
            alt={product!.name}
            className="w-8 h-8 absolute -translate-y-1/2 right-0 top-1/2 me-4 cursor-pointer"
            onClick={
              () => setMainImage(
                (index) => (index + 1) % product!.gallery.length,
              )
            }
          />
          {product!.gallery.length > 0 ? (
            <img
              src={product!.gallery[mainImage]}
              alt={product!.name}
              className="w-full h-full object-contain"
            />
          ) : (
            <div className="w-full h-full flex items-center justify-center">
              <span className="text-gray-400">No image available</span>
            </div>
          )}
        </div>
      </div>
    </div>;
  }
};

export default ProductPage;