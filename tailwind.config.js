/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.{html,js,php}"],
  theme: {
    extend: {},
  },
  plugins: [require('@tailwindcss/forms'),
    require('preline/plugin'),],
}

