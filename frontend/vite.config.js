import { defineConfig } from 'vite';

export default defineConfig({
  root: '.',
  publicDir: 'pages',

  base: './', // ✅ FIX Ở ĐÂY
  
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: 'index.html'
      }
    }
  },

  server: {
    port: 3000,
    open: false,
    host: true
  },

  define: {
    __API_BASE_URL__: JSON.stringify(process.env.API_BASE_URL || 'https://pwa-ecommerce.test/api')
  }
});