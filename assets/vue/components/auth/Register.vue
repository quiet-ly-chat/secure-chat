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

                        <div class="d-flex justify-content-center form_container">
                            <form v-show="!isAuthenticated" autocomplete="off" @submit.prevent="performRegister">
                                <div class="input-group mb-2">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input v-model="login" type="text" placeholder="username" id="username"
                                           class="form-control input_user" name="_username"
                                           required="required" autofocus
                                    />
                                </div>
                                <div v-if="hasRegisterError && error.username !== undefined" class="">
                                    <div role="alert">
                                            <span v-for="oneError in error.username">
                                                <span style="color: red">{{ oneError }}</span>
                                            </span>
                                    </div>
                                </div>
                                <div class="input-group mb-2">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input v-model="password" type="password" placeholder="password"
                                           class="form-control input_pass" id="password" name="_password"
                                           required="required"/>
                                </div>
                                <div v-if="hasRegisterError && error.plainPassword !== undefined" class="">
                                    <div role="alert">
                                            <span v-if="error.plainPassword.first !== undefined"
                                                  v-for="oneError in error.plainPassword.first">
                                                <span style="color: red">{{ oneError }}</span>
                                            </span>
                                    </div>
                                </div>
                                <div class="input-group mb-2">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input v-model="confirmPassword" type="password" placeholder="password"
                                           class="form-control input_pass" id="confirmPassword"
                                           name="_confirm_passwords"
                                           required="required"/>
                                </div>
                                <div v-if="hasRegisterError && error.plainPassword !== undefined" class="">
                                    <div role="alert">
                                        <span v-if="error.plainPassword.second !== undefined"
                                              v-for="oneError in error.plainPassword.second">
                                                <span style="color: red">{{ oneError }}</span>
                                            </span>
                                    </div>
                                </div>
                                <div class="input-group mt-4">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input v-model="keyDecoder" type="password" placeholder="key coder"
                                           class="form-control input_pass" id="keyDecoder" name="keyDecoder"
                                           required="required"/>
                                </div>

                                <div class="d-flex justify-content-center mt-3 login_container">
                                    <button
                                            :disabled="login.length === 0 || password.length === 0 || confirmPassword.length === 0 || isLoading"
                                            type="submit" class="btn login_btn"
                                            style="background-color: #EBEBEB; border: 1px solid #3E8DA2; color: black">
                                        Register
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
    import OpenCrypto from '../../../../public/js/OpenCrypto';

    export default {
        name: 'register',
        data() {
            return {
                login: '',
                password: '',
                confirmPassword: '',
                keyDecoder: '',
            };
        },
        computed: {
            isLoading() {
                return this.$store.getters['security/isLoading'];
            },
            hasRegisterError() {
                return this.$store.getters['security/hasRegisterError'];
            },
            error() {
                return this.$store.getters['security/registerError'];
            },
            isPasswordDecoded() {
                return this.$store.getters['security/isDecoded'];
            },
            isAuthenticated() {
                return this.$store.getters['security/isAuthenticated'];
            },
        },
        methods: {
            performRegister() {
                let redirect = this.$route.query.redirect;
                let data = this.$data;
                let store = this.$store;
                let router = this.$router;

                const crypt = new OpenCrypto();

                crypt.getRSAKeyPair().then(function (keyPair) {

                    crypt.encryptPrivateKey(keyPair.privateKey, data.keyDecoder).then(function (encryptedPrivateKey) {
                        crypt.cryptoPublicToPem(keyPair.publicKey).then(function (publicPem) {
                            store.dispatch('security/register', {
                                ...data,
                                publicKey: publicPem,
                                privateKey: encryptedPrivateKey
                            })
                                .then(() => {
                                    if (!store.getters['security/hasRegisterError']) {
                                        if (typeof redirect !== 'undefined') {
                                            router.push({path: redirect});
                                        } else {
                                            router.push({path: '/login'});
                                        }
                                    }
                                });
                        }).catch(err => {
                            this.$notify({
                                group: 'chat',
                                title: 'There was an error during key conversion.',
                                type: 'error'
                            });
                        })
                    }).catch(err => {
                        this.$notify({
                            group: 'chat',
                            title: 'There was an error during key encryption.',
                            type: 'error'
                        });
                    })
                }).catch(err => {
                    this.$notify({
                        group: 'chat',
                        title: 'There was an error during keys generation.',
                        type: 'error'
                    });
                })
            },
        },
    }
</script>