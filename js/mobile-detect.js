// Mobile detection and redirection script - Created December 29, 2023

function isMobileDevice() {
    const mobileRegex = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i;
    return mobileRegex.test(navigator.userAgent) || window.innerWidth <= 768;
}

function getCurrentPageName() {
    const path = window.location.pathname;
    const page = path.split('/').pop() || 'index.html';
    return page;
}

function getBasePath() {
    if (window.location.protocol === 'file:') {
        const pathParts = window.location.pathname.split('/');
        return pathParts.slice(0, pathParts.length - 1).join('/');
    }
    return window.location.origin;
}

function checkFileExists(url) {
    return new Promise((resolve) => {
        if (window.location.protocol === 'file:') {
            // For file:// protocol, we'll assume the file exists
            // since we can't actually check due to security restrictions
            resolve(true);
        } else {
            fetch(url, { method: 'HEAD' })
                .then(response => resolve(response.ok))
                .catch(() => resolve(false));
        }
    });
}

async function redirectToMobile() {
    if (!isMobileDevice() || window.location.pathname.includes('/m/')) {
        return;
    }

    const currentPage = getCurrentPageName();
    const basePath = getBasePath();
    const mobileUrl = `${basePath}/m/${currentPage}`;

    try {
        const exists = await checkFileExists(mobileUrl);
        if (exists) {
            console.log('Redirecting to mobile version:', mobileUrl);
            window.location.href = mobileUrl;
        } else {
            console.warn('Mobile version not available:', mobileUrl);
        }
    } catch (error) {
        console.error('Error checking mobile version:', error);
    }
}

// Initialize mobile detection
function initMobileDetection() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', redirectToMobile);
    } else {
        redirectToMobile();
    }

    // Handle resize events with debouncing
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (isMobileDevice() && !window.location.pathname.includes('/m/')) {
                redirectToMobile();
            }
        }, 250);
    });
}

// Start mobile detection
initMobileDetection(); 