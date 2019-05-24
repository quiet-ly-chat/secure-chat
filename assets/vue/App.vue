<template>
    <div class="">
        <app-header></app-header>
        <nav class="navbar navbar-expand-lg primary-color"
             style="background-color: #EBEBEB; width: 100%; border-bottom: 1px solid #3E8DA2; color: black">

            <router-link class="navbar-brand" to="/home"><img width="100px" src="/images/logo.png"></router-link>

            <!-- Collapse button -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#basicExampleNav"
                    aria-controls="basicExampleNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible content -->
            <div class="collapse navbar-collapse" id="basicExampleNav"
                 style="display: flex; justify-content: space-between">

                <!-- Links -->
                <ul class="navbar-nav mr-auto">
                    <router-link class="nav-item" tag="li" to="/chat" active-class="active"
                                 v-if="isAuthenticated && isDecoded">
                        <a class="nav-link">Chat</a>
                    </router-link>
                    <router-link class="nav-item" tag="li" to="/admin" active-class="active"
                                 v-if="isAdmin">
                        <a class="nav-link">Users</a>
                    </router-link>
                    <router-link class="nav-item" tag="li" to="/register" active-class="active"
                                 v-if="!isAuthenticated">
                        <a class="nav-link">Register</a>
                    </router-link>
                    <li class="nav-item" v-if="isAuthenticated">
                        <a class="nav-link" href="/api/security/logout">Logout</a>
                    </li>
                    <router-link class="nav-item" tag="li" to="/login" active-class="active"
                                 v-if="!isAuthenticated || !isDecoded">
                        <a class="nav-link" v-if="!isAuthenticated">Login</a>
                        <a class="nav-link" v-else>Decode Key</a>
                    </router-link>

                </ul>
                <ul class="navbar-nav mr-auto" style="margin-right: 60px !important;">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown"
                           aria-haspopup="true"
                           aria-expanded="false">Language</a>
                        <div class="dropdown-menu dropdown-primary selectpicker"
                             aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item">🇬🇧 - English (UK)</a>
                        </div>
                    </li>
                </ul>

            </div>
            <!-- Collapsible content -->
        </nav>
        <notifications group="chat"/>
        <router-view></router-view>
    </div>
</template>

<script>
    import axios from 'axios';
    import Header from './components/header/header.vue'

    export default {
        name: 'app',
        components: {
            'app-header': Header
        },
        created() {
            let isAuthenticated = JSON.parse(this.$parent.$el.attributes['data-is-authenticated'].value),
                roles = JSON.parse(this.$parent.$el.attributes['data-roles'].value),
                privateKey = JSON.parse(this.$parent.$el.attributes['data-private-key'].value),
                userName = JSON.parse(this.$parent.$el.attributes['data-username'].value),
                userId = JSON.parse(this.$parent.$el.attributes['data-user-id'].value),
                publicKey = JSON.parse(this.$parent.$el.attributes['data-public-key'].value);

            let payload = {isAuthenticated, roles, privateKey, userName, userId, publicKey};
            this.$store.dispatch('security/onRefresh', payload);

            axios.interceptors.response.use(undefined, (err) => {
                return new Promise(() => {
                    if (err.response.status === 403) {
                        this.$router.push({path: '/login'})
                    } else if (err.response.status === 500) {
                        document.open();
                        document.write(err.response.data);
                        document.close();
                    }
                    throw err;
                });
            });
        },
        computed: {
            isAuthenticated() {
                return this.$store.getters['security/isAuthenticated']
            },
            isDecoded() {
                return this.$store.getters['security/isDecoded']
            },
            isAdmin() {
                return this.$store.getters['security/hasRole']('ROLE_ADMIN');
            },
        },
    }

</script>

<style>
    .section-title {
        display: flex;
        justify-content: center;
        margin-bottom: 20px
    }

    .invalid input {
        border: 1px solid red;
    }

    p.invalid {
        color: red;
    }

    .default_btn {
        background-color: #EBEBEB;
        border: 1px solid #3E8DA2;
        color: black;
    }
</style>