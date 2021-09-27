module.exports = {
  purge: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  darkMode: 'class', // or 'media' or 'class'
  theme: {
    extend: {},
    minWidth: {
        '0': '0',
        'input': '500px',
    },
    fontSize: {
        'title': '18px',
    },
    textColor: {
        'staleBlue': '#1a0dab'
    }
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
