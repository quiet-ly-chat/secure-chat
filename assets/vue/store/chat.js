import ChatAPI from '../api/chat';
import OpenCrypto from '../../../public/js/OpenCrypto';
import {arrayBufferToText} from '../rsa/helper';

export default {
    namespaced: true,
    state: {
        friends: [],
        steadyFriends: [],
        activeChat: null,
        messages: [],
        decodeError: 0,
        connectedWS: [],
        wsSession: null
    },
    getters: {
        friends(state) {
            return state.friends;
        },
        steadyFriends(state) {
            return state.steadyFriends;
        },
        connectedWS(state) {
            return state.connectedWS;
        },
        activeChat(state) {
            return state.activeChat;
        },
        messages(state) {
            let activeFriendId = '';
            if (state.activeChat) {
                activeFriendId = 'fr' + state.activeChat.friendId;
            }
            if (activeFriendId && state.messages.hasOwnProperty(activeFriendId)) {
                return state.messages[activeFriendId] || [];
            }
            return [];
        },
        decodeError(state) {
            return state.decodeError;
        },
        wsSession(state) {
            return state.wsSession;
        }
    },
    mutations: {
        ['FETCHED_FRIENDS'](state, friends) {
            state.friends = friends;
            state.steadyFriends = friends;
            state.friends = state.friends.map(friend => {
                friend.unRead = false;
                return friend;
            })
        },
        ['UPDATE_MESSAGES'](state, messages) {
            state.messages = messages;
        },
        ['FETCHED_MESSAGES'](state, payload) {
            state.messages = payload.messages;
            state.decodeError = payload.decodeError;
        },
        ['OPENED_CHAT'](state, user) {
            state.activeChat = user;
            if (user) {
                state.friends = state.friends.map(friend => {
                    if (friend.friendId == user.friendId) {
                        friend.unRead = false;
                    }
                    return friend;
                })
            }
        },
        ['NEW_MESSAGE'](state, payload) {
            state.messages['fr' + payload.friendId].push(payload);
            if (!state.activeChat || state.activeChat.friendId != payload.friendId) {
                state.friends = state.friends.map(friend => {
                    if (friend.friendId == payload.friendId) {
                        friend.unRead = true;
                    }
                    return friend;
                });

            }
        },
        ['CONNECTED_TO_WS'](state, connectedFriends) {
            state.connectedWS = connectedFriends;
        },
        ['SET_WS_SESSION'](state, session) {
            state.wsSession = session;
        },
        ['DISCONNECTED_TO_WS'](state, disconnectedFriend) {
            state.connectedWS = state.connectedWS.filter(friend => {
                return friend != disconnectedFriend;
            });
        },
        ['REMOVED_BY_WS'](state, friendId) {
            state.friends = state.friends.filter(friend => {
                return friend.friendId != friendId;
            });
            state.steadyFriends = state.steadyFriends.filter(friend => {
                return friend.friendId != friendId;
            });
            const friendMsgId = 'fr' + friendId;
            const {[friendMsgId]: _, ...newMessages} = state.messages;
            if (state.activeChat.friendId == friendId) {
                state.activeChat = null;
            }
            state.messages = newMessages;
        }
    },
    actions: {
        getFriends({commit}) {
            return ChatAPI.getFriends()
                .then(res => {
                        commit('FETCHED_FRIENDS', JSON.parse(res.data));
                    }
                )
                .catch(err => console.log(err));
        },
        sendFriendRequest({commit, dispatch}, username) {
            return ChatAPI.addNewFriend(username)
                .then(res => {
                })
                .catch(err => console.log(err));
        },
        removeFriendship({commit, dispatch, state}, payload) {
            return ChatAPI.removeFriendship(payload.friendUsername)
                .then(res => {
                        const newFriends = state.friends.filter(function (friend) {
                            return !(friend.friendUsername === payload.friendUsername)
                        });
                        const friendMsgId = 'fr' + payload.friendId;
                        const {[friendMsgId]: _, ...newMessages} = state.messages;
                        if (state.activeChat.friendId == payload.friendId) {
                            commit('OPENED_CHAT', null);
                        }
                        commit('FETCHED_FRIENDS', newFriends);
                        commit('UPDATE_MESSAGES', newMessages);
                    }
                )
                .catch(err => console.log(err));
        },
        setActiveChat({commit}, payload) {
            commit('OPENED_CHAT', payload);
        },
        async getMessagesForAll({commit, state}, payload) {

            let friendsMessages = await ChatAPI.getMessagesForAll();

            const crypto = new OpenCrypto();
            friendsMessages = JSON.parse(friendsMessages.data);

            let decodeError = 0;
            for (let property in friendsMessages) {
                let friendId = property.substr(2);
                let friend = state.friends.filter(friend => {
                    return friend.friendId == friendId;
                })[0];

                await friendsMessages[property].map(async item => {
                    if (item.msgTo.id == friend.friendId) {
                        try {
                            let res = await crypto.rsaDecrypt(payload.privateKey, item.fromMsg);
                            return Object.assign(item, {fromMsg: arrayBufferToText(res)})
                        } catch (e) {
                            decodeError++;
                            return item;
                        }
                    } else {
                        try {
                            let res = await crypto.rsaDecrypt(payload.privateKey, item.toMsg);
                            return Object.assign(item, {toMsg: arrayBufferToText(res)})
                        } catch (e) {
                            decodeError++;
                            return item;
                        }
                    }
                });
            }

            commit('FETCHED_MESSAGES', {messages: friendsMessages, decodeError});
        },
        getMessagesForFriend({commit}, payload) {
            return ChatAPI.getMessagesForFriend(payload).then(res => {

            }).catch(err => console.log(err));
        },
        async addNewMessage({commit, state}, payload) {
            const crypto = new OpenCrypto();

            let friend = state.friends.filter(friend => {
                return friend.friendId == payload.msgTo.id || friend.friendId == payload.msgFrom.id;
            })[0];

            if (payload.msgTo.id == friend.friendId) {
                let res = await crypto.rsaDecrypt(payload.privateKey, payload.fromMsg);
                payload.fromMsg = arrayBufferToText(res);
            } else {
                let res = await crypto.rsaDecrypt(payload.privateKey, payload.toMsg);
                payload.toMsg = arrayBufferToText(res);
            }

            commit('NEW_MESSAGE', Object.assign(payload, {friendId: friend.friendId}));
        },
        connectedToWS({commit}, payload) {
            commit('CONNECTED_TO_WS', payload);
        },
        disconnectedToWS({commit}, payload) {
            commit('DISCONNECTED_TO_WS', payload);
        },
        setWsSession({commit}, payload) {
            commit('SET_WS_SESSION', payload);
        },
        removeFriend({commit}, payload) {
            commit('REMOVED_BY_WS', payload);
        }
    },
}