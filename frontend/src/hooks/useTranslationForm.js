import { useState, useCallback } from 'react';

/**
 * Custom hook for managing translation form state
 * @returns {Object} Form state and handlers
 */
export const useTranslationForm = () => {
    const [formData, setFormData] = useState({
        source: '',
        target: '',
    });
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [errors, setErrors] = useState({});

    /**
     * Update form field value
     * @param {string} field - Field name
     * @param {string} value - Field value
     */
    const updateField = useCallback((field, value) => {
        setFormData(prev => ({
            ...prev,
            [field]: value,
        }));
        
        // Clear field-specific error when user starts typing
        if (errors[field]) {
            setErrors(prev => ({
                ...prev,
                [field]: '',
            }));
        }
    }, [errors]);

    /**
     * Validate form data
     * @returns {boolean} Validation result
     */
    const validateForm = useCallback(() => {
        const newErrors = {};

        if (!formData.source.trim()) {
            newErrors.source = 'Source text is required';
        }

        if (formData.source.trim().length > 1000) {
            newErrors.source = 'Source text is too long (max 1000 characters)';
        }

        if (formData.target.trim().length > 1000) {
            newErrors.target = 'Target text is too long (max 1000 characters)';
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    }, [formData]);

    /**
     * Reset form to initial state
     */
    const resetForm = useCallback(() => {
        setFormData({
            source: '',
            target: '',
        });
        setErrors({});
        setIsSubmitting(false);
    }, []);

    /**
     * Set submitting state
     * @param {boolean} submitting - Submitting state
     */
    const setSubmitting = useCallback((submitting) => {
        setIsSubmitting(submitting);
    }, []);

    /**
     * Set form errors
     * @param {Object} newErrors - Error object
     */
    const setFormErrors = useCallback((newErrors) => {
        setErrors(newErrors);
    }, []);

    return {
        formData,
        isSubmitting,
        errors,
        updateField,
        validateForm,
        resetForm,
        setSubmitting,
        setFormErrors,
    };
}; 