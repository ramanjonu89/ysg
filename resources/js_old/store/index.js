import Vuex from 'vuex';
import Vue from 'vue';
import auth from './modules/auth';
import sidebar from './modules/sidebar';

Vue.use(Vuex);

export default new Vuex.Store({
	modules: {
		auth: auth,
		sidebar: sidebar,
	}
});
