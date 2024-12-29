// Mobile optimization script - Created December 29, 2023

// Image optimization and lazy loading
function optimizeImages() {
    const images = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    loadImage(img);
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.01
        });

        images.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for older browsers
        images.forEach(loadImage);
    }
}

function loadImage(img) {
    const src = img.getAttribute('data-src');
    if (!src) return;

    // Create a new image to check dimensions
    const tempImage = new Image();
    tempImage.onload = function() {
        // Calculate optimal dimensions for mobile
        const maxWidth = window.innerWidth - 30; // Account for padding
        const aspectRatio = this.height / this.width;
        
        if (this.width > maxWidth) {
            img.style.width = maxWidth + 'px';
            img.style.height = (maxWidth * aspectRatio) + 'px';
        }
        
        img.src = src;
        img.removeAttribute('data-src');
    };
    tempImage.src = src;
}

// Handle navigation menu
function setupNavigation() {
    const navToggle = document.querySelector('.nav-toggle');
    const nav = document.querySelector('nav');
    const body = document.body;

    if (navToggle && nav) {
        navToggle.addEventListener('click', function(e) {
            e.preventDefault();
            nav.classList.toggle('active');
            body.style.overflow = nav.classList.contains('active') ? 'hidden' : '';
            this.setAttribute('aria-expanded', nav.classList.contains('active'));
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (nav.classList.contains('active') && 
                !nav.contains(e.target) && 
                !navToggle.contains(e.target)) {
                nav.classList.remove('active');
                body.style.overflow = '';
                navToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && nav.classList.contains('active')) {
                nav.classList.remove('active');
                body.style.overflow = '';
                navToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }
}

// Handle forms
function setupForms() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                    
                    // Add error message
                    let errorMsg = field.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.style.color = 'red';
                        errorMsg.style.fontSize = '14px';
                        errorMsg.style.marginTop = '5px';
                        field.parentNode.insertBefore(errorMsg, field.nextSibling);
                    }
                    errorMsg.textContent = `Please fill in this field`;
                } else {
                    field.classList.remove('error');
                    const errorMsg = field.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                const firstError = form.querySelector('.error');
                if (firstError) {
                    firstError.focus();
                }
            }
        });
    });
}

// Handle tables
function setupTables() {
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
        // Wrap table in container if not already wrapped
        if (!table.parentElement.classList.contains('table-container')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-container';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
}

// Initialize all mobile optimizations
function initMobileOptimizations() {
    document.addEventListener('DOMContentLoaded', function() {
        optimizeImages();
        setupNavigation();
        setupForms();
        setupTables();
        
        // Handle dynamic content loading
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    optimizeImages();
                    setupTables();
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
}

// Start optimizations
initMobileOptimizations(); 