import React from 'react';

/**
 * Loading spinner component
 * @param {Object} props - Component props
 * @param {string} props.message - Loading message
 * @param {string} props.size - Size of spinner (small, medium, large)
 */
const LoadingSpinner = ({ message = 'Loading...', size = 'medium' }) => {
    const sizeClass = `spinner-${size}`;
    
    return (
        <div className="loading-spinner">
            <div className={`spinner ${sizeClass}`}></div>
            {message && <p className="loading-message">{message}</p>}
        </div>
    );
};

export default LoadingSpinner; 