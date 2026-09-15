import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './tests/e2e',
  fullyParallel: true,
  retries: 0,
  use: {
    baseURL: 'http://127.0.0.1:8017',
    trace: 'retain-on-failure',
  },
  webServer: {
    command: 'php -S 127.0.0.1:8017 -t public public/index.php',
    url: 'http://127.0.0.1:8017/_page/health',
    reuseExistingServer: true,
    timeout: 30_000,
  },
});
