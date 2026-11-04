import 'core-js/stable';
import Vue from 'vue';
import App from './App';
import Notifications from 'vue-notification'
import router from './router';
import VeeValidate from 'vee-validate'
import i18n from './i18n';
import CoreuiVue from '@coreui/vue';
import { iconsSet as icons } from './assets/icons/icons.js';
import store from './store';
import './permission';
import VueEvents from 'vue-events';


const veeValidateConfig = {
  aria: true,
  classNames: {},
  classes: false,
  delay: 0,
  dictionary: null,
  errorBagName: "errors", // change if property conflicts
  events: "input|blur",
  fieldsBagName: "veeFields",
  i18n: null, // the vue-i18n plugin instance
  i18nRootKey: "validations", // the nested key under which the validation messages will be located
  inject: true,
  locale: "en",
  validity: false
};

Vue.config.performance = true;
Vue.use(CoreuiVue);
Vue.use(VeeValidate, veeValidateConfig);
Vue.use(Notifications);
Vue.use(VueEvents);
Vue.prototype.$log = console.log.bind(console);

new Vue({
  el: '#app',
  router,
  store,
  icons,
  i18n,
  template: '<App/>',
  components: {
    App
  }
});
