import React, { useState } from 'react';

/**
 * Component for displaying a single translation unit row
 * @param {Object} props - Component props
 * @param {Object} props.translation - Translation unit object
 * @param {boolean} props.isEditing - Whether this item is being edited
 * @param {Function} props.onEdit - Callback to start editing
 * @param {Function} props.onCancel - Callback to cancel editing
 * @param {Function} props.onSave - Callback to save changes
 * @param {Function} props.onDelete - Callback to delete translation
 */
const TranslationItem = ({
    translation,
    isEditing,
    onEdit,
    onCancel,
    onSave,
    onDelete,
}) => {
    const [editValue, setEditValue] = useState(translation.target || '');

    const handleEdit = () => {
        setEditValue(translation.target || '');
        onEdit(translation.id);
    };

    const handleCancel = () => {
        setEditValue(translation.target || '');
        onCancel();
    };

    const handleSave = () => {
        onSave(translation.id, editValue);
    };

    const handleDelete = () => {
        onDelete(translation.id);
    };

    return (
        <tr>
            <td className="source-text">{translation.source}</td>
            <td className="target-text">
                {isEditing ? (
                    <input
                        type="text"
                        value={editValue}
                        onChange={(e) => setEditValue(e.target.value)}
                        className="edit-input"
                        autoFocus
                    />
                ) : (
                    <span className={translation.target ? 'has-translation' : 'no-translation'}>
                        {translation.target || 'No translation yet'}
                    </span>
                )}
            </td>
            <td className="actions">
                {isEditing ? (
                    <>
                        <button onClick={handleSave} className="btn-save">
                            Save
                        </button>
                        <button onClick={handleCancel} className="btn-cancel">
                            Cancel
                        </button>
                    </>
                ) : (
                    <>
                        <button onClick={handleEdit} className="btn-edit">
                            Edit
                        </button>
                        <button onClick={handleDelete} className="btn-delete">
                            Delete
                        </button>
                    </>
                )}
            </td>
        </tr>
    );
};

export default TranslationItem; 