/**
 * CSRF Token Auto-Refresh for Login Page
 * Prevents 419 Page Expired errors by automatically refreshing CSRF tokens
 */

class CsrfTokenManager {
    constructor(options = {}) {
        this.refreshInterval = options.refreshInterval || 50 * 60 * 1000; // 59 minutes default
        this.maxRetries = options.maxRetries || 3;
        this.retryDelay = options.retryDelay || 5000; // 5 seconds
        this.currentRetries = 0;
        this.intervalId = null;
        this.isRefreshing = false;

        this.init();
    }

    init() {
        // Start auto-refresh when page loads
        this.startAutoRefresh();

        // Refresh token before form submission
        this.attachFormSubmitHandler();

        // Handle page visibility changes
        this.handleVisibilityChange();
    }

    startAutoRefresh() {
        // Clear any existing interval
        if (this.intervalId) {
            clearInterval(this.intervalId);
        }

        // Set up periodic refresh
        this.intervalId = setInterval(() => {
            this.refreshToken();
        }, this.refreshInterval);
    }

    stopAutoRefresh() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
    }

    async refreshToken() {
        if (this.isRefreshing) {
            return;
        }

        this.isRefreshing = true;

        try {
            const response = await fetch('/refresh-csrf-token', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            // Handle 419 CSRF token mismatch specifically
            if (response.status === 419) {
                this.handleCsrfMismatch();
                return;
            }

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.csrf_token) {
                this.updateCsrfTokens(data.csrf_token);
                this.currentRetries = 0; // Reset retry counter on success
            } else {
                throw new Error('Invalid response: missing csrf_token');
            }
        } catch (error) {
            this.handleRefreshError();
        } finally {
            this.isRefreshing = false;
        }
    }

    updateCsrfTokens(newToken) {
        // Update meta tag
        const metaTag = document.querySelector('meta[name="csrf-token"]');

        if (metaTag) {
            metaTag.setAttribute('content', newToken);
        }

        // Update all CSRF token inputs
        const csrfInputs = document.querySelectorAll('input[name="_token"]');
        csrfInputs.forEach(input => {
            input.value = newToken;
        });

        // Update axios default header if available
        if (window.axios && window.axios.defaults.headers.common) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
        }
    }

    handleRefreshError() {
        this.currentRetries++;

        if (this.currentRetries < this.maxRetries) {
            setTimeout(() => {
                this.refreshToken();
            }, this.retryDelay);
        } else {
            this.currentRetries = 0; // Reset for next interval
        }
    }

    handleCsrfMismatch() {
        // Stop auto-refresh since we're redirecting
        this.stopAutoRefresh();

        // Add a small delay to ensure any pending operations complete
        setTimeout(() => {
            // Redirect to login page
            window.location.href = '/login';
        }, 1000);
    }

    attachFormSubmitHandler() {
        const loginForm = document.getElementById('formAuthentication');
        if (loginForm) {
            loginForm.addEventListener('submit', async (e) => {
                // Refresh token just before form submission to ensure it's fresh
                if (!this.isRefreshing) {
                    e.preventDefault();
                    await this.refreshToken();

                    // Re-submit the form after token refresh
                    setTimeout(() => {
                        loginForm.submit();
                    }, 100);
                }
            });
        }
    }

    handleVisibilityChange() {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                // Page is hidden, stop auto-refresh to save resources
                this.stopAutoRefresh();
            } else {
                // Page is visible again, refresh token and restart auto-refresh
                this.refreshToken();
                this.startAutoRefresh();
            }
        });
    }

    // Public method to manually refresh token
    manualRefresh() {
        return this.refreshToken();
    }

    // Public method to get current token
    getCurrentToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        return metaTag ? metaTag.getAttribute('content') : null;
    }
}

// Initialize CSRF Token Manager when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize on login page
    if (document.getElementById('formAuthentication')) {
        window.csrfManager = new CsrfTokenManager({
            refreshInterval: 50 * 60 * 1000, // 50 minutes
            maxRetries: 3,
            retryDelay: 5000 // 5 seconds
        });
    }
});

// Export for potential use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CsrfTokenManager;
}
