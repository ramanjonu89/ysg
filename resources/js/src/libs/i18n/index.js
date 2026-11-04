import Vue from 'vue'
import VueI18n from 'vue-i18n'

import en from "vee-validate/dist/locale/en.json"
import Language from "@/utils/language";

Vue.use(VueI18n)

function getVeeValidateLocale(locale) {
  const veeValidateLocales = { en, 'default': en};

  return veeValidateLocales[locale] || veeValidateLocales['default'];
}

function loadLocaleMessages() {
  const locales = require.context('./locales', true, /[A-Za-z0-9-_,\s]+\.json$/i)
  const messages = {}
  locales.keys().forEach(key => {
    const matched = key.match(/([A-Za-z0-9-_]+)\./i)
    if (matched && matched.length > 1) {
      const locale = matched[1]
      messages[locale] = locales(key)
      messages[locale].validation = getVeeValidateLocale(locale).messages
    }
  })
  return messages
}
export default new VueI18n({
  locale: Language.getLanguage(),
  fallbackLocale: Language.getDefaultLanguage(),
  messages: loadLocaleMessages(),
  silentTranslationWarn: true,
});
