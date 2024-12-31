// Mobile navigation functionality - Created December 29, 2023
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const mobileNav = document.getElementById('mobile-nav');
    const body = document.body;

    // Create overlay element
    const overlay = document.createElement('div');
    overlay.className = 'menu-overlay';
    body.appendChild(overlay);

    // Toggle menu function
    function toggleMenu() {
        mobileNav.classList.toggle('active');
        overlay.classList.toggle('active');
        body.style.overflow = mobileNav.classList.contains('active') ? 'hidden' : '';
    }

    // Event listeners
    menuToggle.addEventListener('click', function(e) {
        e.preventDefault();
        toggleMenu();
    });

    overlay.addEventListener('click', function() {
        toggleMenu();
    });

    // Track current page in navigation
    const currentPath = window.location.pathname;
    const currentPage = currentPath.split('/').pop() || 'index.html';
    
    // Get all navigation links
    const navLinks = mobileNav.getElementsByTagName('a');
    Array.from(navLinks).forEach(link => {
        const href = link.getAttribute('href');
        // Only process internal links (not external like Book Now)
        if (href && !href.startsWith('http')) {
            const linkPage = href.split('/').pop();
            if (linkPage === currentPage) {
                link.classList.add('active');
                // Also add current-page class for consistency
                link.classList.add('current-page');
            }
        }

        // Add click handler to close menu
        link.addEventListener('click', function() {
            toggleMenu();
        });
    });

    // Handle swipe gestures
    let touchStartX = 0;
    let touchEndX = 0;

    document.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, false);

    document.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, false);

    function handleSwipe() {
        const swipeThreshold = 50;
        const swipeDistance = touchEndX - touchStartX;

        if (Math.abs(swipeDistance) < swipeThreshold) return;

        if (swipeDistance > 0 && touchStartX < 50) {
            // Swipe right from left edge
            if (!mobileNav.classList.contains('active')) {
                toggleMenu();
            }
        } else if (swipeDistance < 0 && mobileNav.classList.contains('active')) {
            // Swipe left when menu is open
            toggleMenu();
        }
    }

    // Handle escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileNav.classList.contains('active')) {
            toggleMenu();
        }
    });

    // Analytics tracking
    const trackOutboundLink = function(url) {
        if (typeof gtag === 'function') {
            gtag('event', 'click', {
                'event_category': 'outbound',
                'event_label': url,
                'transport_type': 'beacon'
            });
        }
    };

    // Track outbound links
    document.querySelectorAll('a[href^="http"]').forEach(link => {
        link.addEventListener('click', function(e) {
            trackOutboundLink(this.href);
        });
    });
}); 