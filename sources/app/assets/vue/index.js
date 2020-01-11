import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import Axios from 'axios'
import VuePaginate from 'vuejs-paginate'

Vue.prototype.$http = Axios;

const token = localStorage.getItem('token');
if (token) {
  Vue.prototype.$http.defaults.headers.common['Authorization'] = 'Bearer ' + token
  Vue.prototype.$http.defaults.headers.common['Accept'] = 'application/json'
}

Vue.config.productionTip = false

Vue.component('paginate', VuePaginate);

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')