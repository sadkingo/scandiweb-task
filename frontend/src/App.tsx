import React, { Suspense, lazy, useEffect, useState } from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import Navigation from './components/Navigation';
import { ROUTES } from './config';
import { useCategories } from './hooks/useCategories';
import Loader from "./components/UI/Loader";

// Lazy load page components for better performance
const CategoryPage = lazy(() => import('@/pages/CategoryPage'));
const ProductPage = lazy(() => import('@/pages/ProductPage'));

/**
 * Main application component
 * Handles routing and layout structure
 */
const App: React.FC = () => {
    const {categories, loading} = useCategories();
    const [defaultCategory, setDefaultCategory] = useState('');

    useEffect(() => {
        if (categories.length > 0) {
            setDefaultCategory(categories[0]?.name?.toLowerCase());
        }
    }, [categories]);

    return (
        <div className="app min-h-screen min-w-96 flex flex-col items-center">
            <header className="w-full">
                <Navigation/>
            </header>
            <main className="max-w-6xl mx-auto px-4 py-8 pt-[160px]! flex-grow w-full">
                <Suspense fallback={<Loader/>}>
                    {loading ? (
                        <Loader/>
                    ) : (
                        <Routes>
                            {/* Redirect root to first category page */}
                            <Route path={ROUTES.HOME}
                                   element={defaultCategory ? <Navigate to={`/${defaultCategory}`} replace/> :
                                       <Loader/>}/>

                            {/* Dynamic routes for each category */}
                            {categories.map(category => (
                                <Route
                                    key={category.id}
                                    path={`/${category.name.toLowerCase()}`}
                                    element={<CategoryPage categoryId={+category.id} category={category.name}/>}
                                />
                            ))}

                            {/* Product Page*/}
                            <Route path={ROUTES.PRODUCT} element={<ProductPage/>}/>

                            {/* Catch all route for 404 */}
                            <Route path="*"
                                   element={defaultCategory ? <Navigate to={`/${defaultCategory}`} replace/> :
                                       <Loader/>}/>
                        </Routes>
                    )}
                </Suspense>
            </main>
        </div>
    );
};

export default App;