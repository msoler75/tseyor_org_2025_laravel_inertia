// playwright.config.cjs
// Configuración básica para Playwright en este proyecto
// Docs: https://playwright.dev/docs/test-configuration

/** @type {import('@playwright/test').PlaywrightTestConfig} */
const config = {
  use: {
    baseURL: 'http://localhost',
    headless: true,
    viewport: { width: 1280, height: 800 },
    ignoreHTTPSErrors: true,
    video: 'retain-on-failure',
  },
  testDir: './tests/e2e',
  mcp: {
    servers: {
      Context7: {
        type: 'stdio',
        command: 'npx',
        args: ['-y', '@upstash/context7-mcp@latest']
      },
      playwright: {
        command: 'npx',
        args: ['@playwright/mcp@latest']
      },
      aiVisionDebug: {
        type: 'http',
        url: 'https://himcp.ai/server/ai-vision-debug-mcp-server'
      }
    }
  }
};

module.exports = config;
