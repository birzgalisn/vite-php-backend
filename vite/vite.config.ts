import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import liveReload from 'vite-plugin-live-reload';
import { resolve } from 'node:path';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react(), liveReload([import.meta.dirname + '/../public/**/*.php'])],

  root: 'src',
  base: '/',
  publicDir: '../../public',

  build: {
    outDir: '../../public/dist',
    emptyOutDir: true,
    copyPublicDir: false,
    manifest: true,
    rollupOptions: {
      input: resolve(import.meta.dirname, 'src/main.tsx'),
      output: {
        manualChunks(id) {
          if (id.includes('node_modules')) {
            return 'vendor';
          }
        },
      },
    },
  },
});
