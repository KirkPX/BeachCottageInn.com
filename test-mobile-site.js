// Mobile site testing script - Created December 29, 2023
const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

const MOBILE_DEVICES = [
    {
        name: 'iPhone X',
        userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
        viewport: { width: 375, height: 812, deviceScaleFactor: 3, isMobile: true, hasTouch: true }
    },
    {
        name: 'Pixel 2',
        userAgent: 'Mozilla/5.0 (Linux; Android 8.0; Pixel 2 Build/OPD3.170816.012) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3765.0 Mobile Safari/537.36',
        viewport: { width: 411, height: 731, deviceScaleFactor: 2.625, isMobile: true, hasTouch: true }
    }
];

const PAGES_TO_TEST = [
    'index.html',
    'rates-and-rooms.html',
    'contact.html',
    'what-to-see-and-do.html',
    'directions.html',
    'oceanfront-apartment-rental.html'
];

async function testMobileSite() {
    const browser = await puppeteer.launch();
    const screenshotsDir = path.join(__dirname, 'test-screenshots');
    
    // Create screenshots directory if it doesn't exist
    if (!fs.existsSync(screenshotsDir)) {
        fs.mkdirSync(screenshotsDir);
    }

    // Test each device
    for (const device of MOBILE_DEVICES) {
        console.log(`Testing on ${device.name}...`);
        const deviceDir = path.join(screenshotsDir, device.name.toLowerCase().replace(' ', '-'));
        
        if (!fs.existsSync(deviceDir)) {
            fs.mkdirSync(deviceDir);
        }

        // Test each page
        for (const page of PAGES_TO_TEST) {
            const testPage = await browser.newPage();
            
            // Set device emulation
            await testPage.setUserAgent(device.userAgent);
            await testPage.setViewport(device.viewport);

            try {
                // Load the page
                await testPage.goto(`file://${path.join(__dirname, page)}`, {
                    waitUntil: 'networkidle0',
                    timeout: 30000
                });

                // Test mobile redirect
                const redirected = await testPage.evaluate(() => {
                    return window.location.pathname.includes('/m/');
                });

                if (!redirected) {
                    console.warn(`Warning: ${page} did not redirect to mobile version on ${device.name}`);
                }

                // Take screenshot
                await testPage.screenshot({
                    path: path.join(deviceDir, `${page.replace('.html', '')}.png`),
                    fullPage: true
                });

                // Test responsive elements
                const responsiveIssues = await testPage.evaluate(() => {
                    const issues = [];
                    
                    // Check for horizontal overflow
                    if (document.documentElement.scrollWidth > document.documentElement.clientWidth) {
                        issues.push('Horizontal scroll detected');
                    }

                    // Check image sizes
                    const images = document.querySelectorAll('img');
                    images.forEach(img => {
                        if (img.offsetWidth > document.documentElement.clientWidth) {
                            issues.push(`Image too wide: ${img.src}`);
                        }
                    });

                    // Check tap target sizes
                    const tapTargets = document.querySelectorAll('a, button');
                    tapTargets.forEach(target => {
                        const rect = target.getBoundingClientRect();
                        if (rect.width < 44 || rect.height < 44) {
                            issues.push(`Tap target too small: ${target.textContent || target.className}`);
                        }
                    });

                    return issues;
                });

                if (responsiveIssues.length > 0) {
                    console.warn(`Issues found on ${page} (${device.name}):`);
                    responsiveIssues.forEach(issue => console.warn(`- ${issue}`));
                }

                // Test navigation menu
                const menuWorks = await testPage.evaluate(() => {
                    const navToggle = document.querySelector('.nav-toggle');
                    const nav = document.querySelector('nav');
                    
                    if (!navToggle || !nav) return false;
                    
                    navToggle.click();
                    const isMenuVisible = nav.classList.contains('active');
                    navToggle.click();
                    const isMenuHidden = !nav.classList.contains('active');
                    
                    return isMenuVisible && isMenuHidden;
                });

                if (!menuWorks) {
                    console.warn(`Warning: Navigation menu not working properly on ${page} (${device.name})`);
                }

                console.log(`✓ Tested ${page} on ${device.name}`);
            } catch (error) {
                console.error(`Error testing ${page} on ${device.name}:`, error);
            }

            await testPage.close();
        }
    }

    await browser.close();
    console.log('Mobile site testing completed!');
}

// Run the tests
testMobileSite().catch(console.error); 