import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '../store';
import Home from '../components/home/Home';
import Login from '../components/auth/Login';
import Register from "../components/auth/Register";
import DefineUsers from "../components/admin/DefineUsers";
import Chat from "../views/Chat";

Vue.use(VueRouter);

let router = new VueRouter({
    mode: 'history',
    routes: [
        {path: '/home', component: Home},
        {path: '/register', component: Register},
        {path: '/login', component: Login},
        {path: '/chat', component: Chat, meta: {requiresAuth: true, requiresDecoded: true}},
        {path: '/admin', component: DefineUsers, meta: {requiresAuth: true, requiresAdmin: true}},
        {path: '*', redirect: '/home'}
    ],
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (store.getters['security/isAuthenticated']) {
            if (to.matched.some(record => record.meta.requiresAdmin)) {
                if (store.getters['security/hasRole']('ROLE_ADMIN')) {
                    next();
                } else {
                    next({
                        path: '/home',
                        query: {redirect: to.fullPath}
                    });
                }
            }
            if (to.matched.some(record => record.meta.requiresDecoded)) {
                if (store.getters['security/isDecoded']) {
                    next();
                } else {
                    next({
                        path: '/login',
                        query: {redirect: to.fullPath}
                    });
                }
            }
            next();
        } else {
            next({
                path: '/login',
                query: {redirect: to.fullPath}
            });
        }
    } else {
        next();
    }
});

export default router;