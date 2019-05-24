<template>
    <div style="margin: 50px 10px 0">
        <div class="messaging" style="height: 85vh">
            <div class="inbox_msg" style="display: flex; height: 100%;">
                <list style="flex: 1"></list>
                <div class="mesgs">
                    <div v-if="activeUser"
                         class="active_chat_header">
                        User: {{ activeUser.friendUsername }}
                    </div>
                    <messages></messages>

                    <div class="type_msg" v-if="activeUser" style="flex:1">
                        <div class="input_msg_write" style="border: 1px solid #3E8DA2">
                            <form @submit.prevent="sendMessage">
                                <div style="display: flex;" class="input-group">
                                    <input style="flex: 9" type="text" class="form-control" placeholder="Type a message"
                                           :maxlength="120"
                                           v-model="message"/>
                                    <div class="input-addon letters-counter" v-text="(120 - message.length)"></div>
                                    <div class="input-addon">
                                        <button class="msg_send_btn" type="submit"><i
                                                class="fas fa-paper-plane"
                                                aria-hidden="true"></i></button>
                                    </div>
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

    import List from '../components/chat/List';
    import Messages from '../components/chat/Messages';
    import OpenCrypto from '../../../public/js/OpenCrypto';
    import {textToArrayBuffer} from '../rsa/helper';

    export default {
        name: "Chat",
        components: {
            List,
            Messages
        },
        data() {
            return {
                message: '',
                messageShowed: false
            }
        },
        computed: {
            myFriends() {
                return this.$store.getters['chat/steadyFriends'];
            },
            myUsername() {
                return this.$store.getters['security/userName'];
            },
            myPublicKey() {
                return this.$store.getters['security/publicKey'];
            },
            myDecryptedKey() {
                return this.$store.getters['security/decryptedKey'];
            },
            myId() {
                return this.$store.getters['security/userId'];
            },
            activeUser() {
                this.message = '';
                return this.$store.getters['chat/activeChat'];
            },
            decodeError() {
                return this.$store.getters['chat/decodeError'];
            },
            connectedWS() {
                return this.$store.getters['chat/connectedWS'];
            },
            wsSession() {
                return this.$store.getters['chat/wsSession'];
            }
        },
        async created() {
            await this.$store.dispatch('chat/getFriends');
            this.$store.dispatch('chat/getMessagesForAll', {privateKey: this.$store.getters['security/decryptedKey']});
        },
        beforeUpdate() {
            if (this.decodeError > 0 && !this.messageShowed) {
                this.$notify({
                    group: 'chat',
                    title: `Couldn't decrypt ${this.decodeError} messages`,
                    type: 'error'
                });
                this.messageShowed = true;
            }
        },
        beforeMount() {
            if (!this.wsSession) {
                const webSocket = WS.connect(process.env.VUE_APP_WEBSOCKET_URL);
                let websocketSession = null;
                let store = this.$store;
                webSocket.on("socket/connect", function (session) {
                    store.dispatch('chat/setWsSession', session);

                    websocketSession = session;
                    return session;
                });
                webSocket.on("socket/disconnect", function (error) {
                    console.log("Disconnected for " + error.reason + " with code " + error.code);
                });
            }
        },
        watch: {
            async myFriends(newFriends, oldFriends) {
                const store = this.$store;
                const myKey = this.myDecryptedKey;

                const friends = newFriends.filter(friend => {
                    return this.connectedWS.indexOf(friend.friendId) < 0
                });

                let sess = this.wsSession;
                if (friends.length > 0) {
                    if (sess) {
                        for (let friend in friends) {
                            friend = friends[friend];
                            let roomNr = friend.senderId + '-' + friend.accepterId;
                            sess.subscribe("ws\/chat\/" + roomNr, function (uri, payload) {

                                if (payload.hasOwnProperty('chatEvent')) {
                                    store.dispatch('chat/addNewMessage', Object.assign(JSON.parse(payload.chatEvent), {
                                        privateKey: myKey
                                    }));
                                } else if (payload.hasOwnProperty('removeFriend')) {
                                    store.dispatch('chat/removeFriend', payload.friendId);
                                    sess.unsubscribe(uri);
                                    store.dispatch('chat/disconnectedToWS', payload.friendId);
                                }
                            });
                        }
                        this.$store.dispatch('chat/connectedToWS', this.connectedWS.concat(friends.map(friend => {
                            return friend.friendId
                        })));
                    }
                }
            }
        },
        methods: {
            async sendMessage() {
                if (this.message) {
                    const crypto = new OpenCrypto();
                    const activeUser = this.activeUser;
                    const message = this.message;
                    const myPubKey = this.myPublicKey;

                    try {
                        const friendCryptoPublic = await crypto.pemPublicToCrypto(this.activeUser.friendPubKey);
                        const myCryptoPublic = await crypto.pemPublicToCrypto(myPubKey);
                        const friendEncryptedMessage = await crypto.rsaEncrypt(friendCryptoPublic, textToArrayBuffer(message));
                        const myEncrypted = await crypto.rsaEncrypt(myCryptoPublic, textToArrayBuffer(message));
                        if (this.wsSession) {
                            this.wsSession.publish("ws\/chat\/" + activeUser.senderId + "-" + activeUser.accepterId, {
                                forFriend: friendEncryptedMessage,
                                forUser: myEncrypted,
                                msg: message
                            });

                            this.message = '';
                        } else {
                            this.$notify({
                                group: 'chat',
                                title: 'Couldn\'t send a message. Connection is not established.',
                                type: 'error'
                            });
                        }
                    } catch (e) {
                        this.$notify({
                            group: 'chat',
                            title: 'There was an error during message encryption.',
                            type: 'error'
                        });
                    }
                }
            }
        }
    }
</script>
<style scoped>
    input:focus, button:focus {
        outline: none;
    }

    .active_chat_header {
        flex: 0.5;
        background-color: #ebebeb;
        border: 1px darkgray solid;
        justify-content: center;
        align-items: center;
        display: flex;
    }

    .input-addon {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .letters-counter {
        background: #05728f;
        color: #ffffff
    }
</style>