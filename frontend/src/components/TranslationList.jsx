import React, { useState } from 'react';
import TranslationItem from './TranslationItem';

/**
 * Component for displaying a list of translation units
 * @param {Object} props - Component props
 * @param {Array} props.translations - Array of translation units
 * @param {Function} props.onUpdate - Callback function when translation is updated
 * @param {Function} props.onDelete - Callback function when translation is deleted
 */
const TranslationList = ({ translations, onUpdate, onDelete }) => {
    const [editingId, setEditingId] = useState(null);

    const handleEdit = (id) => {
        setEditingId(id);
    };

    const handleCancelEdit = () => {
        setEditingId(null);
    };

    const handleSave = async (id, newTarget) => {
        try {
            await onUpdate(id, newTarget);
            setEditingId(null);
        } catch (error) {
            console.error('Error updating translation:', error);
        }
    };

    const handleDelete = async (id) => {
        if (window.confirm('Are you sure you want to delete this translation?')) {
            try {
                await onDelete(id);
            } catch (error) {
                console.error('Error deleting translation:', error);
            }
        }
    };

    if (translations.length === 0) {
        return (
            <div className="translation-list">
                <h2>Translation Units (0)</h2>
                <div className="no-translations">
                    No translations found. Add some using the form above!
                </div>
            </div>
        );
    }

    return (
        <div className="translation-list">
            <h2>Translation Units ({translations.length})</h2>
            <table className="translations-table">
                <thead>
                    <tr>
                        <th>Source Text</th>
                        <th>Target Translation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {translations.map((translation) => (
                        <TranslationItem
                            key={translation.id}
                            translation={translation}
                            isEditing={editingId === translation.id}
                            onEdit={handleEdit}
                            onCancel={handleCancelEdit}
                            onSave={handleSave}
                            onDelete={handleDelete}
                        />
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default TranslationList; 