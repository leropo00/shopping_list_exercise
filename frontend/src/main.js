import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router.js'
import Notifications from '@kyvg/vue3-notification'
import {createPinia} from 'pinia'
import {createI18n} from 'vue-i18n' 
import sl from "./locales/sl.json" 
import en from "./locales/en.json" 

const pinia = createPinia();

const i18n = createI18n({ 
  locale: navigator.language, 
  fallbackLocale: "sl", 
  messages: { sl, en }, 
  legacy: false 
})

createApp(App)
    .use(router)
    .use(pinia)
    .use(i18n) 
    .use(Notifications)
    .mount('#app');
