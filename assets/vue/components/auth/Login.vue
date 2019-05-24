<template>
    <div class="main-page">
        <div class="row col">
            <div class="container" style="height: 100vh;">
                <div class="" style="display: flex; justify-content: center; align-items: center; height: 100vh">
                    <div class="user_card"
                         style="padding: 50px; background: #FAFAFA; border: 1px solid #3E8DA2; min-width: 40%; position: relative; top: -100px">
                        <div class="d-flex justify-content-center">
                            <div class="brand_logo_container" style="margin-bottom: 10px">
                                <img src="/images/logo.png" class="brand_logo" alt="Logo" width="250px">
                            </div>
                        </div>
                        <div v-if="isLoading" class="">
                            <p>Loading...</p>
                        </div>

                        <div v-else-if="hasError" class="">
                            <div class="alert alert-danger" role="alert">
                                {{ error }}
                            </div>
                        </div>
                        <div class="d-flex justify-content-center form_container">
                            <form v-show="!isAuthenticated" @submit.prevent="performLogin" autocomplete="off">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input v-model="login" type="text" placeholder="username" id="username"
                                           class="form-control input_user" name="_username"
                                           required="required"
                                           autofocus/>
                                </div>
                                <div class="input-group mb-2">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input v-model="password" type="password" placeholder="password"
                                           class="form-control input_pass" id="password" name="_password"
                                           required="required"/>
                                </div>

                                <div class="d-flex justify-content-center mt-3 login_container">
                                    <button
                                            :disabled="login.length === 0 || password.length === 0 || isLoading"
                                            type="submit" class="btn login_btn"
                                            style="background-color: #EBEBEB; border: 1px solid #3E8DA2; color: black">
                                        Login
                                    </button>
                                </div>
                            </form>
                            <form v-show="isAuthenticated && !isPasswordDecoded" @submit.prevent="decodeKey"
                                  autocomplete="off">
                                <div class="input-group mb-2">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input ref="keyDec" v-model="keyDecoder" type="password" placeholder="Key Decoder"
                                           class="form-control input_pass" id="keyDecoder" name="keyDecoder"
                                           required="required"/>
                                </div>
                                <div class="d-flex justify-content-center mt-3 login_container">
                                    <button
                                            :disabled="keyDecoder.length === 0 || isLoading"
                                            type="submit" class="btn login_btn default_btn">
                                        Decode Key
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: 'login',
        data() {
            return {
                login: '',
                password: '',
                keyDecoder: '',
            };
        },
        mounted() {
            let redirect = this.$route.query.redirect;
            if (this.$store.getters['security/isAuthenticated'] && this.$store.getters['security/isDecoded']) {
                if (typeof redirect !== 'undefined') {
                    this.$router.push({path: redirect});
                } else {
                    this.$router.push({path: '/home'});
                }
            }
        },
        computed: {
            isLoading() {
                return this.$store.getters['security/isLoading'];
            },
            hasError() {
                return this.$store.getters['security/hasError'];
            },
            error() {
                return this.$store.getters['security/error'];
            },
            isPasswordDecoded() {
                return this.$store.getters['security/isDecoded'];
            },
            isAuthenticated() {
                return this.$store.getters['security/isAuthenticated'];
            },
        },
        methods: {
            performLogin() {
                let payload = {login: this.$data.login, password: this.$data.password};
                this.$store.dispatch('security/login', payload)
                    .then(() => {
                        if (!this.$store.getters['security/hasError'] && this.$store.getters['security/isDecoded']) {
                            this.$router.push({path: '/chat'});
                        }
                        this.$refs.keyDec.focus();
                    });
            },
            async decodeKey() {
                await this.$store.dispatch('security/decodePassword', {key: this.keyDecoder});

                if (!this.$store.getters['security/hasError'] && this.$store.getters['security/isDecoded']) {
                    this.$router.push({path: '/chat'});
                }
            }
        },
    }
</script>