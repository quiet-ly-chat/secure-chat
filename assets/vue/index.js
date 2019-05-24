import Vue from 'vue';
import App from './App';
import router from './router';
import store from './store';

import Vuelidate from 'vuelidate';
import Notifications from 'vue-notification'

Vue.use(Vuelidate);
Vue.use(Notifications);

new Vue({
    template: '<App/>',
    components: {App},
    router,
    store,
}).$mount('#app');