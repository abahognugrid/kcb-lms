/**
 * Global CSRF Error Handler
 * Handles 419 CSRF token mismatch errors across the entire application
 */

(function() {
    'use strict';

    // Global handler for CSRF token mismatch errors
    function handleGlobalCsrfError(response, xhr) {
        console.warn('Global CSRF token mismatch detected');
        
        // Show user-friendly notification
        showCsrfErrorNotification();
        
        // Redirect to login after a short delay
        setTimeout(() => {
            window.location.href = '/login';
        }, 3000);
    }

    // Show user-friendly notification
    function showCsrfErrorNotification() {
        // Try to use existing notification system first
        if (typeof window.showNotification === 'function') {
            window.showNotification('Your session has expired. Redirecting to login page...', 'warning');
            return;
        }

        // Fallback: Create a simple notification
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 15px 20px;
            z-index: 9999;
            font-family: Arial, sans-serif;
            font-size: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 300px;
        `;
        notification.innerHTML = `
            <strong>Session Expired</strong><br>
            Your session has expired. Redirecting to login page...
        `;
        
        document.body.appendChild(notification);
        
        // Remove notification after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }

    // Setup global AJAX error handling for jQuery (if available)
    if (typeof $ !== 'undefined' && $.ajaxSetup) {
        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            if (xhr.status === 419) {
                handleGlobalCsrfError(xhr.responseJSON, xhr);
            }
        });
    }

    // Setup global fetch error handling
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch.apply(this, args)
            .then(response => {
                if (response.status === 419) {
                    handleGlobalCsrfError(response);
                    return Promise.reject(new Error('CSRF token mismatch'));
                }
                return response;
            });
    };

    // Setup global XMLHttpRequest error handling
    const originalXHROpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
        this.addEventListener('readystatechange', function() {
            if (this.readyState === 4 && this.status === 419) {
                handleGlobalCsrfError(this.responseText, this);
            }
        });
        
        return originalXHROpen.apply(this, arguments);
    };

    // Setup global axios error handling (if available)
    if (typeof window.axios !== 'undefined') {
        window.axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response && error.response.status === 419) {
                    handleGlobalCsrfError(error.response.data, error.response);
                }
                return Promise.reject(error);
            }
        );
    }

    // Handle form submission errors
    document.addEventListener('DOMContentLoaded', function() {
        // Listen for form submission responses
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.tagName === 'FORM') {
                // Add a one-time listener for the response
                const originalAction = form.action;
                const originalMethod = form.method;
                
                // Only handle POST forms (where CSRF is required)
                if (originalMethod.toLowerCase() === 'post') {
                    form.addEventListener('error', function(errorEvent) {
                        // This is a fallback - most CSRF errors will be caught by other handlers
                        console.log('Form submission error detected');
                    });
                }
            }
        });
    });

    // Expose global function for manual CSRF error handling
    window.handleCsrfError = handleGlobalCsrfError;

    console.log('Global CSRF error handler initialized');
})();
