import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";
import daisyui from "daisyui";

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    "./vendor/laravel/jetstream/**/*.blade.php",
    "./storage/framework/views/*.php",
    "./resources/views/**/*.blade.php",
    "./resources/js/**/*.vue",
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ["Figtree", ...defaultTheme.fontFamily.sans],
      },
    },
  },

  daisyui: {
    themes: [
      {
        tseyor: {
          primary: "#38bdf8",
          secondary: "#cc5c2c",
          accent: "#eefc80",
          neutral: "#1b1826",
          "base-100": "#fff",
          "base-200": "#eef0f1",
          "base-300": "#dae4e9",
          info: "#81d5f8",
          success: "#40dd7c",
          warning: "#f6cc37",
          error: "#f63d2c",
        },
      },
    ],
  },
  plugins: [forms, typography, daisyui],
};
