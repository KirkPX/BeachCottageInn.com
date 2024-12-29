// Mobile site testing script - Created December 29, 2023
// This script helps validate mobile pages by taking screenshots and testing responsive behavior

const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

const MOBILE_VIEWPORT = {
    width: 375,
    height: 812,
    deviceScaleFactor: 2,
    isMobile: true,
    hasTouch: true
};

const PAGES_TO_TEST = [
    'index.html',
    'what-to-see-and-do.html',
    'directions.html',
    'contact.html',
    'rates-and-rooms.html'
];

async function testMobilePages() {
    console.log('Starting mobile page tests...');
    const browser = await puppeteer.launch();
    const results = {
        passed: [],
        failed: [],
        warnings: []
    };

    try {
        for (const page of PAGES_TO_TEST) {
            console.log(`\nTesting ${page}...`);
            const testResults = await testPage(browser, page);
            
            results.passed.push(...testResults.passed);
            results.failed.push(...testResults.failed);
            results.warnings.push(...testResults.warnings);
            
            // Take a screenshot for visual inspection
            const screenshotPath = `./test-screenshots/${page.replace('.html', '')}-mobile.png`;
            await takeScreenshot(browser, page, screenshotPath);
        }
    } catch (error) {
        console.error('Error during tests:', error);
    } finally {
        await browser.close();
    }

    // Output results
    outputResults(results);
}

async function testPage(browser, pageName) {
    const page = await browser.newPage();
    await page.setViewport(MOBILE_VIEWPORT);
    
    const results = {
        passed: [],
        failed: [],
        warnings: []
    };

    try {
        // Load the page
        await page.goto(`file://${path.resolve(__dirname, 'm', pageName)}`, {
            waitUntil: 'networkidle0'
        });

        // Test viewport meta tag
        const viewportMeta = await page.$eval('meta[name="viewport"]', el => el.content);
        if (viewportMeta.includes('width=device-width') && viewportMeta.includes('initial-scale=1')) {
            results.passed.push(`${pageName}: Viewport meta tag properly set`);
        } else {
            results.failed.push(`${pageName}: Missing or incorrect viewport meta tag`);
        }

        // Test mobile navigation
        const hasHamburgerMenu = await page.$('#menu-toggle');
        const hasMobileNav = await page.$('#mobile-nav');
        if (hasHamburgerMenu && hasMobileNav) {
            results.passed.push(`${pageName}: Mobile navigation present`);
        } else {
            results.failed.push(`${pageName}: Missing mobile navigation elements`);
        }

        // Test responsive images
        const images = await page.$$eval('img', imgs => imgs.map(img => ({
            src: img.src,
            hasWidth: img.hasAttribute('width'),
            hasHeight: img.hasAttribute('height'),
            hasAlt: img.hasAttribute('alt')
        })));
        
        images.forEach(img => {
            if (!img.hasAlt) {
                results.warnings.push(`${pageName}: Image missing alt text: ${img.src}`);
            }
        });

        // Test touch targets
        const touchTargets = await page.$$eval('a, button', elements => 
            elements.map(el => {
                const style = window.getComputedStyle(el);
                const rect = el.getBoundingClientRect();
                return {
                    width: rect.width,
                    height: rect.height,
                    padding: style.padding
                };
            })
        );

        touchTargets.forEach((target, index) => {
            if (target.width < 44 || target.height < 44) {
                results.warnings.push(`${pageName}: Touch target ${index + 1} might be too small`);
            }
        });

        // Test font sizes
        const textElements = await page.$$eval('p, h1, h2, h3, a', elements =>
            elements.map(el => {
                const style = window.getComputedStyle(el);
                return parseFloat(style.fontSize);
            })
        );

        textElements.forEach((size, index) => {
            if (size < 14) {
                results.warnings.push(`${pageName}: Text element ${index + 1} might be too small (${size}px)`);
            }
        });

        // Test horizontal scrolling
        const hasHorizontalScroll = await page.evaluate(() => {
            return document.documentElement.scrollWidth > document.documentElement.clientWidth;
        });

        if (hasHorizontalScroll) {
            results.failed.push(`${pageName}: Page has horizontal scrolling`);
        } else {
            results.passed.push(`${pageName}: No horizontal scrolling`);
        }

        // Test required elements
        const requiredElements = {
            header: await page.$('#mobile-header'),
            nav: await page.$('#mobile-nav'),
            main: await page.$('main'),
            footer: await page.$('footer')
        };

        Object.entries(requiredElements).forEach(([element, exists]) => {
            if (exists) {
                results.passed.push(`${pageName}: Has ${element} element`);
            } else {
                results.failed.push(`${pageName}: Missing ${element} element`);
            }
        });

        // Test CSS and JS files
        const hasRequiredFiles = await page.evaluate(() => {
            const hasStyles = document.querySelector('link[href="mobile-styles.css"]');
            const hasScript = document.querySelector('script[src="mobile.js"]');
            return { hasStyles, hasScript };
        });

        if (hasRequiredFiles.hasStyles) {
            results.passed.push(`${pageName}: Mobile styles linked`);
        } else {
            results.failed.push(`${pageName}: Missing mobile styles`);
        }

        if (hasRequiredFiles.hasScript) {
            results.passed.push(`${pageName}: Mobile script linked`);
        } else {
            results.failed.push(`${pageName}: Missing mobile script`);
        }

        // Test analytics
        const hasAnalytics = await page.$('script[src*="googletagmanager"]');
        if (hasAnalytics) {
            results.passed.push(`${pageName}: Analytics present`);
        } else {
            results.warnings.push(`${pageName}: Missing analytics`);
        }

    } catch (error) {
        results.failed.push(`${pageName}: Error during testing: ${error.message}`);
    } finally {
        await page.close();
    }

    return results;
}

async function takeScreenshot(browser, pageName, outputPath) {
    const page = await browser.newPage();
    await page.setViewport(MOBILE_VIEWPORT);
    
    try {
        await page.goto(`file://${path.resolve(__dirname, 'm', pageName)}`, {
            waitUntil: 'networkidle0'
        });
        
        // Ensure directory exists
        const dir = path.dirname(outputPath);
        if (!fs.existsSync(dir)) {
            fs.mkdirSync(dir, { recursive: true });
        }
        
        await page.screenshot({
            path: outputPath,
            fullPage: true
        });
        
        console.log(`Screenshot saved: ${outputPath}`);
    } catch (error) {
        console.error(`Error taking screenshot of ${pageName}:`, error);
    } finally {
        await page.close();
    }
}

function outputResults(results) {
    console.log('\n=== Test Results ===\n');
    
    console.log('✅ Passed Tests:');
    results.passed.forEach(result => console.log(`  ${result}`));
    
    if (results.failed.length > 0) {
        console.log('\n❌ Failed Tests:');
        results.failed.forEach(result => console.log(`  ${result}`));
    }
    
    if (results.warnings.length > 0) {
        console.log('\n⚠️ Warnings:');
        results.warnings.forEach(result => console.log(`  ${result}`));
    }
    
    console.log('\nSummary:');
    console.log(`Total Passed: ${results.passed.length}`);
    console.log(`Total Failed: ${results.failed.length}`);
    console.log(`Total Warnings: ${results.warnings.length}`);
}

// Run the tests
testMobilePages(); 