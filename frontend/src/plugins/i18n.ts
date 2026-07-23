import { createI18n } from 'vue-i18n'
import it from '@/locales/it.json'
import en from '@/locales/en.json'

const savedLocale = localStorage.getItem('locale')
const isValidLocale = savedLocale === 'it' || savedLocale === 'en'

const i18n = createI18n({
  locale: isValidLocale ? savedLocale : 'it',
  fallbackLocale: 'en',
  legacy: false,
  messages: { it, en }
})

export default i18n