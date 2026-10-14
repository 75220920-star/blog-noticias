import { defineConfig, devices } from '@playwright/test';
process.env.BLOG_DB_DSN ??= 'mysql:host=127.0.0.1;port=3306;dbname=blog_noticias_test;charset=utf8mb4';
process.env.BLOG_BASE_PATH = '';
process.env.BLOG_URL = 'http://127.0.0.1:8088';
export default defineConfig({
  testDir: './tests',
  testMatch: '**/*.spec.js',
  fullyParallel: false,
  workers: 1,
  retries: 0,
  timeout: 45000,
  expect: { timeout: 8000 },
  reporter: [['list'], ['html', { open: 'never' }], ['json', { outputFile: 'work/playwright-results.json' }]],
  use: { baseURL: 'http://127.0.0.1:8088', trace: 'retain-on-failure', screenshot: 'only-on-failure' },
  projects: [
    { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
    { name: 'Pixel 5', use: { ...devices['Pixel 5'] } },
    { name: 'iPhone 12', use: { ...devices['iPhone 12'] } },
    { name: 'Gama baja', use: { browserName: 'chromium', viewport: { width: 360, height: 640 }, isMobile: true, hasTouch: true } },
    { name: 'firefox', use: { ...devices['Desktop Firefox'] }, testMatch: '**/mobile/*.spec.js' }
  ],
  webServer: {
    command: 'php tests/support/serve.php',
    url: 'http://127.0.0.1:8088/login',
    reuseExistingServer: false,
    timeout: 30000,
    env: { BLOG_DB_DSN: process.env.BLOG_DB_DSN, BLOG_BASE_PATH: '', BLOG_URL: process.env.BLOG_URL }
  }
});
