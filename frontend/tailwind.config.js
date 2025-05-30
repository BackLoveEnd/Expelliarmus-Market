/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
  darkMode: "class",
  theme: {
    extend: {
      fontFamily: {
        sans: ["Poppins", "sans-serif"],
      },
      spacing: {
        272: "17rem",
        352: "22rem",
        670: "45rem",
      },
      fontSize: {
        54: "3.375rem",
      },
      width: {
        68: "16.875rem",
      },
      maxHeight: {
        712: "44.5rem",
      },
      boxShadow: {
        "dark-mode": "rgba(255, 255, 255, 0.24) 0px 3px 8px",
        "light-mode": "rgba(0, 0, 0, 0.24) 0px 3px 8px",
      },
    },
  },
  plugins: [require("tailwindcss-primeui"), require("@tailwindcss/typography")],
};
