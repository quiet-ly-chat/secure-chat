import AdminAPI from '../api/admin';

export default {
    namespaced: true,
    state: {
        usernames: [],
        // usernameAdded: false
    },
    getters: {
        usernames(state) {
            return state.usernames;
        },
    },
    mutations: {
        ['FETCHED_USERS'](state, usernames) {
            state.usernames = usernames;
        },
    },
    actions: {
        getUsernames({commit}) {
            return AdminAPI.getUsernames()
                .then(res => {
                        commit('FETCHED_USERS', JSON.parse(res.data));
                    }
                )
                .catch(err => console.log(err));
        },
        addUsername({commit, dispatch}, username) {
            return AdminAPI.addUsername(username)
                .then(res => {
                        dispatch('getUsernames');
                    }
                )
                .catch(err => console.log(err));
        },
        removeUsername({commit, dispatch}, username) {
            return AdminAPI.removeUsername(username)
                .then(res => {
                        dispatch('getUsernames');
                    }
                )
                .catch(err => console.log(err));
        },
    },
}