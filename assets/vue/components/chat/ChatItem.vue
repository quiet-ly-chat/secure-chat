<template>
    <div class="chat_list" @click="changeActiveUser"
         :class="{'active_chat': getActiveUser && friendUsername == getActiveUser.friendUsername, 'msg-waiting': friend.unRead}">
        <div class="chat_people">
            <div class="chat_ib">
                <h5>{{ friendUsername }}
                    <span class="chat_date"></span>
                </h5>
                <p style="float:right"><i @click="removeFriendship" class="fas fa-user-slash"
                                          style="color: red"></i></p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ChatItem",
        props: ['friend'],
        computed: {
            friendUsername() {
                return this.friend.friendUsername;
            },
            getActiveUser() {
                return this.$store.getters['chat/activeChat'];
            },
            wsSession() {
                return this.$store.getters['chat/wsSession'];
            },
            myId() {
                return this.$store.getters['security/userId'];
            }
        },
        methods: {
            removeFriendship() {
                if (window.confirm('Do you want delete friendship with ' + this.friendUsername + '?')) {
                    let roomNr = this.friend.senderId + '-' + this.friend.accepterId;
                    let friendId = this.myId;
                    if (this.wsSession) {
                        this.wsSession.publish("ws\/chat\/" + roomNr, {
                            removeFriend: true,
                            friendId
                        });
                        this.wsSession.unsubscribe("ws\/chat\/" + roomNr)
                    }
                    this.$store.dispatch('chat/removeFriendship', {
                        friendUsername: this.friendUsername,
                        friendId: this.friend.friendId
                    });
                    this.$store.dispatch('chat/disconnectedToWS', this.friend.friendId);
                }
            },
            changeActiveUser() {
                this.$store.dispatch('chat/setActiveChat', this.friend);
            }
        }
    }
</script>

<style scoped>
    .inbox_chat .chat_list.msg-waiting {
        background-color: #3E8DA2;
        animation-name: bgColor;
        animation-duration: 3s;
        animation-iteration-count: infinite;
    }

    .inbox_chat .chat_list.msg-waiting h5 {
        color: #464646;
        animation-name: color;
        animation-duration: 3s;
        animation-iteration-count: infinite;
    }

    @keyframes bgColor {
        0% {
            background-color: #3E8DA2;
        }
        50% {
            background-color: #05728f;
        }
        100% {
            background-color: #3E8DA2;
        }
    }

    @keyframes color {
        0% {
            color: #464646;
            /*color: #ffffff;*/
        }
        50% {
            color: #ffffff;
        }
        100% {
            color: #464646;
            /*color: #ffffff;*/
        }
    }
</style>