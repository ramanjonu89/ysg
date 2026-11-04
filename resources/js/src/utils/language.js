const LANG_KEY = 'sc_lang'
const DEFAULT_LANGUAGE = 'en'

const Language = {
  getDefaultLanguage() {
    return DEFAULT_LANGUAGE;
  },

  getLanguage() {
    return localStorage.getItem(LANG_KEY) || this.getDefaultLanguage()
  },

  setLanguage(lang) {
    localStorage.setItem(LANG_KEY, lang || this.getDefaultLanguage())
  },
}

export default Language
