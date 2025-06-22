import React from 'react';

/**
 * Error message component
 * @param {Object} props - Component props
 * @param {string} props.message - Error message
 * @param {Function} props.onDismiss - Callback to dismiss error
 * @param {boolean} props.dismissible - Whether error can be dismissed
 */
const ErrorMessage = ({ message, onDismiss, dismissible = true }) => {
    if (!message) return null;

    return (
        <div className="error-message-container">
            <div className="error">
                <span className="error-text">{message}</span>
                {dismissible && onDismiss && (
                    <button 
                        onClick={onDismiss} 
                        className="error-dismiss"
                        aria-label="Dismiss error"
                    >
                        ×
                    </button>
                )}
            </div>
        </div>
    );
};

export default ErrorMessage; 