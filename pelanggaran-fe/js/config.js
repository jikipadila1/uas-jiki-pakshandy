// Configuration for Pelanggaran Frontend Application

const CONFIG = {
    API_BASE_URL: 'http://localhost:8080/api',
    TOKEN_KEY: 'pelanggaran_token',
    USER_KEY: 'pelanggaran_user',

    // Store token to localStorage
    setToken: function(token) {
        localStorage.setItem(this.TOKEN_KEY, token);
    },

    // Get token from localStorage
    getToken: function() {
        return localStorage.getItem(this.TOKEN_KEY);
    },

    // Remove token from localStorage
    removeToken: function() {
        localStorage.removeItem(this.TOKEN_KEY);
        localStorage.removeItem(this.USER_KEY);
    },

    // Set user data
    setUser: function(user) {
        localStorage.setItem(this.USER_KEY, JSON.stringify(user));
    },

    // Get user data
    getUser: function() {
        const user = localStorage.getItem(this.USER_KEY);
        return user ? JSON.parse(user) : null;
    },

    // Check if user is logged in
    isAuthenticated: function() {
        return this.getToken() !== null;
    },

    // Make API call with authentication
    apiCall: async function(endpoint, options = {}) {
        const token = this.getToken();

        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': token ? `Bearer ${token}` : ''
            }
        };

        const mergedOptions = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers
            }
        };

        try {
            const response = await fetch(`${this.API_BASE_URL}${endpoint}`, mergedOptions);

            // Handle 401 Unauthorized - token expired
            if (response.status === 401) {
                this.removeToken();
                window.location.href = 'login.html';
                return;
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },

    // Format date
    formatDate: function(dateString) {
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    },

    // Show alert
    showAlert: function(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    },

    // Redirect to login if not authenticated
    requireAuth: function() {
        if (!this.isAuthenticated()) {
            window.location.href = 'login.html';
        }
    },

    // Redirect to dashboard if authenticated
    redirectIfAuthenticated: function() {
        if (this.isAuthenticated()) {
            window.location.href = 'dashboard.html';
        }
    }
};
