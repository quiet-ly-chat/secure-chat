import SecurityAPI from '../api/security';
import OpenCrypto from '../../../public/js/OpenCrypto';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        registerError: null,
        isAuthenticated: false,
        roles: [],
        isDecoded: false,
        registrationSuccess: false,
        privateKey: '',
        publicKey: '',
        decryptedKey: '',
        userName: '',
        userId: ''
    },
    getters: {
        isLoading(state) {
            return state.isLoading;
        },
        hasError(state) {
            return state.error !== null;
        },
        hasRegisterError(state) {
            return state.registerError !== null;
        },
        error(state) {
            return state.error;
        },
        registerError(state) {
            return state.registerError;
        },
        isAuthenticated(state) {
            return state.isAuthenticated;
        },
        hasRole(state) {
            return role => {
                return state.roles.indexOf(role) !== -1;
            }
        },
        isDecoded(state) {
            return state.isDecoded;
        },
        decryptedKey(state) {
            return state.decryptedKey;
        },
        privateKey(state) {
            return state.privateKey;
        },
        publicKey(state) {
            return state.publicKey;
        },
        userName(state) {
            return state.userName;
        },
        userId(state) {
            return state.userId;
        }
    },
    mutations: {
        ['AUTHENTICATING'](state) {
            state.isLoading = true;
            state.error = null;
            state.isAuthenticated = false;
            state.roles = [];
        },
        ['AUTHENTICATING_SUCCESS'](state, userData) {
            state.isLoading = false;
            state.error = null;
            state.isAuthenticated = true;
            state.roles = userData.roles;
            state.privateKey = userData.privateKey;
            state.publicKey = userData.publicKey;
            state.userName = userData.login;
        },
        ['AUTHENTICATING_ERROR'](state, error) {
            state.isLoading = false;
            // state.error = error;
            state.error = 'Incorrect data';
            state.isAuthenticated = false;
            state.roles = [];
            state.privateKey = '';
            state.publicKey = '';
            state.decryptedKey = ''
        },
        ['DECODING'](state) {
            state.isLoading = true;
            state.isAuthenticated = true;
            state.error = null;
        },
        ['DECODING_SUCCESS'](state, decodedPrivateKey) {
            state.isLoading = false;
            state.error = null;
            state.isDecoded = true;
            state.decryptedKey = decodedPrivateKey;
        },
        ['DECODING_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
            state.isDecoded = false;
        },
        ['REGISTRATION_ERROR'](state, error) {
            state.isLoading = false;
            state.registerError = error;
            state.registrationSuccess = false;
        },
        ['REGISTRATION_SUCCESS'](state) {
            state.isLoading = false;
            state.registerError = null;
            state.registrationSuccess = true;
        },
        ['PROVIDING_DATA_ON_REFRESH_SUCCESS'](state, payload) {
            state.isLoading = false;
            state.error = null;
            state.isAuthenticated = payload.isAuthenticated;
            state.roles = payload.roles;
            state.privateKey = payload.privateKey;
            state.userName = payload.userName;
            state.userId = payload.userId;
            state.publicKey = payload.publicKey;
            state.isDecoded = false;
        },
    },
    actions: {
        login({commit}, payload) {
            commit('AUTHENTICATING');
            return SecurityAPI.login(payload.login, payload.password)
                .then(res => {
                        commit('AUTHENTICATING_SUCCESS', {...res.data, login: payload.login});
                    }
                )
                .catch(err => commit('AUTHENTICATING_ERROR', err));
        },
        onRefresh({commit}, payload) {
            commit('PROVIDING_DATA_ON_REFRESH_SUCCESS', payload);
        },
        async decodePassword({commit, state}, payload) {
            commit('DECODING');
            const crypt = new OpenCrypto();
            try {
                const decryptedPrivateKey = await crypt.decryptPrivateKey(state.privateKey, payload.key);
                commit('DECODING_SUCCESS', decryptedPrivateKey);
            } catch (e) {
                commit('DECODING_ERROR', e);
            }
        },
        register({commit}, payload) {
            return SecurityAPI.register(payload)
                .then(res =>
                    commit('REGISTRATION_SUCCESS', res)
                )
                .catch(err => {
                    commit('REGISTRATION_ERROR', err.response.data.errors);
                });
        }
    },
}