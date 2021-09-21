module.exports = {
  purge: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {},
    minWidth: {
        '0': '0',
        'input': '500px',
        '1/2': '50%',
        '3/4': '75%',
        'full': '100%',
    }
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
