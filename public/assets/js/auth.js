// API Base URL
const API_URL = 'http://localhost:8080/api';

// Get token from localStorage
function getToken() {
    return localStorage.getItem('token');
}

// Get user from localStorage
function getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

// Check if token is valid
async function isTokenValid() {
    const token = getToken();

    if (!token) {
        return false;
    }

    try {
        const response = await fetch(`${API_URL}/auth/me`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            }
        });

        if (response.status === 401) {
            // Token is invalid or expired
            logout();
            return false;
        }

        const data = await response.json();
        return data.status === true;
    } catch (error) {
        console.error('Token validation error:', error);
        return false;
    }
}

// Make authenticated API call
async function apiCall(endpoint, method = 'GET', body = null) {
    const token = getToken();

    if (!token) {
        logout();
        return null;
    }

    const headers = {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    };

    const config = {
        method: method,
        headers: headers
    };

    if (body) {
        config.body = JSON.stringify(body);
    }

    try {
        const response = await fetch(`${API_URL}${endpoint}`, config);

        if (response.status === 401) {
            // Token is invalid or expired
            logout();
            return null;
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('API call error:', error);
        return null;
    }
}

// Logout
function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = 'login.html';
}

// Check authentication on page load
async function checkAuth() {
    const isValid = await isTokenValid();

    if (!isValid) {
        logout();
        return false;
    }

    return true;
}

// Show alert
function showAlert(message, type = 'danger') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';

    alertDiv.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv && alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return date.toLocaleDateString('id-ID', options);
}

// Get badge color for pelanggaran type
function getPelanggaranBadge(type) {
    const badges = {
        'contraflow': 'bg-danger',
        'overspeed': 'bg-warning',
        'traffic_jam': 'bg-info'
    };
    return badges[type] || 'bg-secondary';
}

// Get badge color for objek type
function getObjekBadge(type) {
    const badges = {
        'truk': 'bg-primary',
        'mobil': 'bg-success',
        'motor': 'bg-info'
    };
    return badges[type] || 'bg-secondary';
}

// Format pelanggaran type
function formatPelanggaranType(type) {
    const types = {
        'contraflow': 'Contraflow',
        'overspeed': 'Overspeed',
        'traffic_jam': 'Traffic Jam'
    };
    return types[type] || type;
}

// Format objek type
function formatObjekType(type) {
    const types = {
        'truk': 'Truk',
        'mobil': 'Mobil',
        'motor': 'Motor'
    };
    return types[type] || type;
}

// Set current date
function setCurrentDate() {
    const dateElement = document.getElementById('currentDate');
    if (dateElement) {
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        dateElement.textContent = new Date().toLocaleDateString('id-ID', options);
    }
}

// Set user name
function setUserName() {
    const user = getUser();
    const userNameElement = document.getElementById('userName');
    if (userNameElement && user) {
        userNameElement.textContent = user.full_name || user.username;
    }
}

// Initialize auth check and user info
async function initAuth() {
    const authenticated = await checkAuth();

    if (authenticated) {
        setUserName();
        setCurrentDate();
    }
}

// Execute on DOM content loaded
document.addEventListener('DOMContentLoaded', async function() {
    await initAuth();

    // Setup logout button
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();

            if (confirm('Apakah Anda yakin ingin logout?')) {
                logout();
            }
        });
    }

    // Setup active menu
    const currentPage = window.location.pathname.split('/').pop() || 'dashboard.html';
    const menuLinks = document.querySelectorAll('.sidebar-menu a[data-page]');

    menuLinks.forEach(link => {
        const page = link.getAttribute('data-page');
        if (currentPage.includes(page)) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
});
