/**
 * Login Form Script
 * Handles password visibility toggle and form interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize password toggle functionality
    initPasswordToggle();
    
    // Initialize form interactions
    initFormInteractions();
    
    // Initialize remember me functionality
    initRememberMe();
});

/**
 * Initialize password visibility toggle
 */
function initPasswordToggle() {
    const toggleButtons = document.querySelectorAll('.toggle-password');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const input = document.getElementById('password');
            const icon = this.querySelector('.visibility-icon');

            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        });
    });
}

/**
 * Initialize form interactions
 */
function initFormInteractions() {
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        // Add loading state on form submission
        loginForm.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            
            if (submitButton) {
                // Optional: Add loading state
                // submitButton.disabled = true;
                // submitButton.innerHTML = '<span>Loading...</span>';
            }
        });
        
        // Real-time input validation
        const inputs = loginForm.querySelectorAll('input[type="text"], input[type="password"]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateInput(this);
            });
            
            input.addEventListener('input', function() {
                // Remove error state when user starts typing
                this.classList.remove('ring-2', 'ring-error');
            });
        });
    }
}

/**
 * Validate individual input
 */
function validateInput(input) {
    const value = input.value.trim();
    
    if (input.name === 'username') {
        if (value.length === 0) {
            showFieldError(input, 'Username atau email tidak boleh kosong');
        } else {
            clearFieldError(input);
        }
    }
    
    if (input.name === 'password') {
        if (value.length === 0) {
            showFieldError(input, 'Password tidak boleh kosong');
        } else {
            clearFieldError(input);
        }
    }
}

/**
 * Show field error
 */
function showFieldError(input, message) {
    const container = input.closest('.space-y-2');
    
    // Remove existing error message
    const existingError = container.querySelector('.text-error.text-sm');
    if (existingError) {
        existingError.remove();
    }
    
    // Add error class to input
    input.classList.add('ring-2', 'ring-error');
    
    // Add error message
    const errorEl = document.createElement('p');
    errorEl.className = 'text-error text-sm';
    errorEl.textContent = message;
    container.appendChild(errorEl);
}

/**
 * Clear field error
 */
function clearFieldError(input) {
    const container = input.closest('.space-y-2');
    
    // Remove error classes
    input.classList.remove('ring-2', 'ring-error');
    
    // Remove error message
    const errorEl = container.querySelector('.text-error.text-sm');
    if (errorEl) {
        errorEl.remove();
    }
}

/**
 * Initialize remember me functionality
 */
function initRememberMe() {
    const rememberCheckbox = document.getElementById('remember');
    const usernameInput = document.getElementById('identity');
    
    if (rememberCheckbox && usernameInput) {
        // Load saved username if exists
        const savedUsername = localStorage.getItem('remember_username');
        if (savedUsername) {
            usernameInput.value = savedUsername;
            rememberCheckbox.checked = true;
        }
        
        // Save username on form submit if remember me is checked
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function() {
                if (rememberCheckbox.checked) {
                    localStorage.setItem('remember_username', usernameInput.value);
                } else {
                    localStorage.removeItem('remember_username');
                }
            });
        }
    }
}

/**
 * Utility function to add animation classes
 */
function addAnimation(element, animationClass) {
    element.classList.add(animationClass);
    
    element.addEventListener('animationend', function() {
        element.classList.remove(animationClass);
    }, { once: true });
}

/**
 * Export functions for external use
 */
export {
    initPasswordToggle,
    initFormInteractions,
    initRememberMe,
    validateInput,
    showFieldError,
    clearFieldError,
    addAnimation
};
