import { defineConfig } from "vitest/config";

export default defineConfig({
  test: {
    environment: "happy-dom",
    include: ["resources/js/**/*.test.js"],
    globals: false,
    testTimeout: 15000,
  },
});