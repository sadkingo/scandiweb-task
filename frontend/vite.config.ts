import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import React from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [
    tailwindcss(),
    React(),
  ],
  server: {
    proxy: {
      '/graphql/': {
        target: 'http://scandiweb-task.test',
        changeOrigin: true,
        secure: false,
      },
    },
  },
  resolve: {
    alias: {
      '@': '/src',
      '@components': '/src/components',
      '@config': '/src/config',
      '@constants': '/src/constants',
      '@graphql': '/src/graphql',
      '@hooks': '/src/hooks',
      '@pages': '/src/pages',
      '@store': '/src/store',
      '@types': '/src/types',
      '@utils': '/src/utils',
      '@images': '/public/images',
    },
  },
})