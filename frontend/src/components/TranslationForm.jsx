import React from 'react';
import { useTranslationForm } from '../hooks/useTranslationForm';

/**
 * Component for adding new translation units
 * @param {Object} props - Component props
 * @param {Function} props.onAdd - Callback function when translation is added
 */
const TranslationForm = ({ onAdd }) => {
    const {
        formData,
        isSubmitting,
        errors,
        updateField,
        validateForm,
        resetForm,
        setSubmitting,
    } = useTranslationForm();

    const handleSubmit = async (e) => {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }
        
        setSubmitting(true);
        try {
            await onAdd({
                source: formData.source.trim(),
                target: formData.target.trim(),
            });
            resetForm();
        } catch (error) {
            console.error('Error adding translation:', error);
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <div className="translation-form">
            <h2>Add New Translation Unit</h2>
            <form onSubmit={handleSubmit}>
                <div className="form-group">
                    <label htmlFor="source">Source Text (Required):</label>
                    <textarea
                        id="source"
                        placeholder="Enter the text to be translated..."
                        value={formData.source}
                        onChange={(e) => updateField('source', e.target.value)}
                        required
                        rows="3"
                        className={`form-input ${errors.source ? 'error' : ''}`}
                    />
                    {errors.source && (
                        <div className="error-message">{errors.source}</div>
                    )}
                </div>
                
                <div className="form-group">
                    <label htmlFor="target">Target Translation (Optional):</label>
                    <textarea
                        id="target"
                        placeholder="Enter the translation..."
                        value={formData.target}
                        onChange={(e) => updateField('target', e.target.value)}
                        rows="3"
                        className={`form-input ${errors.target ? 'error' : ''}`}
                    />
                    {errors.target && (
                        <div className="error-message">{errors.target}</div>
                    )}
                </div>
                
                <button 
                    type="submit" 
                    disabled={isSubmitting || !formData.source.trim()}
                    className="btn-submit"
                >
                    {isSubmitting ? 'Adding...' : 'Add Translation Unit'}
                </button>
            </form>
        </div>
    );
};

export default TranslationForm; 