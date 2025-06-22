import { useState, useEffect, useCallback } from 'react';
import ApiService from '../services/api';

/**
 * Custom hook for managing translation state and operations
 * @returns {Object} Translation state and operations
 */
export const useTranslations = () => {
    const [translations, setTranslations] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    /**
     * Fetch all translations
     */
    const fetchTranslations = useCallback(async () => {
        try {
            setLoading(true);
            setError(null);
            const data = await ApiService.fetchTranslations();
            setTranslations(data);
        } catch (err) {
            setError(err.message);
            console.error('Error fetching translations:', err);
        } finally {
            setLoading(false);
        }
    }, []);

    /**
     * Add a new translation
     * @param {Object} translation - Translation data
     */
    const addTranslation = useCallback(async (translation) => {
        try {
            setError(null);
            await ApiService.createTranslation(translation);
            await fetchTranslations(); // Refresh the list
        } catch (err) {
            setError(err.message);
            throw err;
        }
    }, [fetchTranslations]);

    /**
     * Update a translation
     * @param {string} id - Translation ID
     * @param {Object} updates - Update data
     */
    const updateTranslation = useCallback(async (id, updates) => {
        try {
            setError(null);
            await ApiService.updateTranslation(id, updates);
            await fetchTranslations(); // Refresh the list
        } catch (err) {
            setError(err.message);
            throw err;
        }
    }, [fetchTranslations]);

    /**
     * Delete a translation
     * @param {string} id - Translation ID
     */
    const deleteTranslation = useCallback(async (id) => {
        try {
            setError(null);
            await ApiService.deleteTranslation(id);
            await fetchTranslations(); // Refresh the list
        } catch (err) {
            setError(err.message);
            throw err;
        }
    }, [fetchTranslations]);

    /**
     * Get a translation by ID
     * @param {string} id - Translation ID
     * @returns {Object|null} Translation object or null
     */
    const getTranslationById = useCallback((id) => {
        return translations.find(translation => translation.id === id) || null;
    }, [translations]);

    /**
     * Clear error state
     */
    const clearError = useCallback(() => {
        setError(null);
    }, []);

    // Fetch translations on mount
    useEffect(() => {
        fetchTranslations();
    }, [fetchTranslations]);

    return {
        translations,
        loading,
        error,
        addTranslation,
        updateTranslation,
        deleteTranslation,
        getTranslationById,
        fetchTranslations,
        clearError,
    };
}; 