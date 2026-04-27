import { defineConfig } from "vite";
import { resolve } from "path";

export default defineConfig({
  build: {
    outDir: "public/build",
    manifest: true,
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: resolve(import.meta.dirname, "resources/js/app.js"),
      },
    },
  },
  server: {
    cors: true,
  },
});
