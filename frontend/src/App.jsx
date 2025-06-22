import React from 'react';
import { useTranslations } from './hooks/useTranslations';
import TranslationForm from './components/TranslationForm';
import TranslationList from './components/TranslationList';
import LoadingSpinner from './components/LoadingSpinner';
import ErrorMessage from './components/ErrorMessage';

/**
 * Main application component
 */
function App() {
    const {
        translations,
        loading,
        error,
        addTranslation,
        updateTranslation,
        deleteTranslation,
        clearError,
    } = useTranslations();

    if (loading) {
        return (
            <div className="app">
                <h1>Robert CAT Tool</h1>
                <LoadingSpinner message="Loading translations..." />
            </div>
        );
    }

    return (
        <div className="app">
            <h1>Robert CAT Tool</h1>
            
            <ErrorMessage 
                message={error} 
                onDismiss={clearError}
                dismissible={true}
            />
            
            <TranslationForm onAdd={addTranslation} />
            <TranslationList 
                translations={translations} 
                onUpdate={updateTranslation}
                onDelete={deleteTranslation}
            />
        </div>
    );
}

export default App; 