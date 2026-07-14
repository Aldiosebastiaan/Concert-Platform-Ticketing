import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  compatibilityDate: "2025-07-15",
  devtools: { enabled: true },

  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE_URL || 'http://127.0.0.1:8000',
    }
  },

  typescript: {
    tsConfig: {
      compilerOptions: {
        types: ["node"]
      }
    }
  },

  css: [
    "~/assets/css/main.css",
  ],

  vite: {
    plugins: [
      tailwindcss(),
    ],
  },
});