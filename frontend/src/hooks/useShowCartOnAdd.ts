import { useEffect } from 'react';

interface UseShowCartOnAddProps {
    lastAddedItem: any;
    setShowCart: (show: boolean) => void;
    clearLastAddedItem: () => void;
}

const useShowCartOnAdd = ({lastAddedItem, setShowCart, clearLastAddedItem}: UseShowCartOnAddProps) => {
    useEffect(() => {
        if (lastAddedItem) {
            setShowCart(true);
            clearLastAddedItem();
        }
    }, [lastAddedItem, setShowCart, clearLastAddedItem]);
};

export default useShowCartOnAdd;