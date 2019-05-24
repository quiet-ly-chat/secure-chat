import Vue from 'vue';
import Vuex from 'vuex';
import SecurityModule from './security';
import AdminModule from './admin';
import ChatModule from './chat';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        security: SecurityModule,
        admin: AdminModule,
        chat: ChatModule
    },
});