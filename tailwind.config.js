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

  plugins: [forms, typography, daisyui],

  theme: {
    container: {
      padding: {
        DEFAULT: "1rem",
        sm: "2rem",
        lg: "4rem",
        xl: "5rem",
        "2xl": "6rem",
      },
    },
    extend: {
      fontFamily: {
        sans: ["Figtree", ...defaultTheme.fontFamily.sans],
      },
    },
  },

  daisyui: {
    themes: [
      {
        summer: {
          primary: "#38bdf8",
          secondary: "#cc5c2c",
          accent: "#eefc80",
          neutral: "#1b1826",
          "base-100": "#fff",
          "base-200": "#e7f4ff",
          "base-300": "#d4e3ff",
          "base-400": "#c1d2f7",
          info: "#81d5f8",
          success: "#40dd7c",
          warning: "#f6cc37",
          error: "#f63d2c",
        },
        winter: {
          primary: "#90cdf4",
          secondary: "#f6ad55",
          accent: "#fefcbf",
          neutral: "#f7fafc",
          "base-100": "#1a202c",
          "base-200": "#2d3748",
          "base-300": "#4a5568",
          "base-400": "#718096",
          info: "#63b3ed",
          success: "#48bb78",
          warning: "#f6e05e",
          error: "#fc8181",
        },
      },
    ],
  },
  darkMode: ["class", '[data-theme="winter"]'],
};
