/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/View/Components/**/*.php",
    "./app/Http/Livewire/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3496D7',
          600: '#2475b0',
          700: '#1a5a8a',
          800: '#0f4a6a',
          900: '#06344a',
        }
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
  // Optimize output - remove unused CSS in production
  safelist: [
    'primary-50', 'primary-100', 'primary-200', 'primary-300', 'primary-400',
    'primary-500', 'primary-600', 'primary-700', 'primary-800', 'primary-900',
  ],
}

