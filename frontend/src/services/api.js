/**
 * API Service for Robert CAT Tool
 * Handles all communication with the backend API
 */

const API_BASE_URL = '/api/translations.php';

class ApiService {
    /**
     * Fetch all translation units
     * @returns {Promise<Array>} Array of translation units
     */
    static async fetchTranslations() {
        try {
            const response = await fetch(API_BASE_URL);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Error fetching translations:', error);
            throw new Error('Failed to fetch translations');
        }
    }

    /**
     * Fetch a specific translation unit by ID
     * @param {string} id - Translation unit ID
     * @returns {Promise<Object>} Translation unit object
     */
    static async fetchTranslationById(id) {
        try {
            const response = await fetch(`${API_BASE_URL}/${id}`);
            if (!response.ok) {
                if (response.status === 404) {
                    throw new Error('Translation unit not found');
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Error fetching translation:', error);
            throw error;
        }
    }

    /**
     * Create a new translation unit
     * @param {Object} translation - Translation unit data
     * @param {string} translation.source - Source text
     * @param {string} translation.target - Target text (optional)
     * @returns {Promise<Object>} Created translation unit
     */
    static async createTranslation(translation) {
        try {
            const response = await fetch(API_BASE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(translation),
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Error creating translation:', error);
            throw error;
        }
    }

    /**
     * Update a translation unit
     * @param {string} id - Translation unit ID
     * @param {Object} updates - Update data
     * @param {string} updates.target - New target text
     * @returns {Promise<Object>} Updated translation unit
     */
    static async updateTranslation(id, updates) {
        try {
            const response = await fetch(`${API_BASE_URL}/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(updates),
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Error updating translation:', error);
            throw error;
        }
    }

    /**
     * Delete a translation unit
     * @param {string} id - Translation unit ID
     * @returns {Promise<boolean>} Success status
     */
    static async deleteTranslation(id) {
        try {
            const response = await fetch(`${API_BASE_URL}/${id}`, {
                method: 'DELETE',
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            return true;
        } catch (error) {
            console.error('Error deleting translation:', error);
            throw error;
        }
    }
}

export default ApiService; 