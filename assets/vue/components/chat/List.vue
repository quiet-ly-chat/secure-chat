<template>
    <div class="inbox_people">
        <div class="headind_srch" style="display: flex; justify-content: space-between">
            <div style="flex: 1; max-width: 45%">
                <div>
                    <i class="fas fa-user-plus" @click="addNewFriend = !addNewFriend"></i>
                    <div v-if="addNewFriend">
                        <form @submit.prevent="sendFriendRequest">
                            <input type="text" v-model="newFriendName" style="max-width: 100%"
                                   autocomplete="off">
                            <button class="btn login_btn default_btn" type="submit"
                                    style="padding: 3px 8px; margin-top: 2px">
                                Add
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="srch_bar" style="flex: 1; min-width: 50%">
                <div class="stylish-input-group">
                    <input type="text" class="search-bar" placeholder="Search" v-model="searchFriend">
                    <span class="input-group-addon">
                                    <button type="button"><i class="fa fa-search" aria-hidden="true"></i></button>
                                </span>
                </div>
            </div>
        </div>
        <div class="inbox_chat">
            <chat-item v-for="(friend) in myFriends" :friend="friend"
                       :key="friend.friendId + (friend.unRead ? '1' : '0')"></chat-item>
        </div>
    </div>
</template>

<script>
    import ChatItem from './ChatItem';

    export default {
        name: "List",
        components: {
            ChatItem
        },
        data() {
            return {
                addNewFriend: false,
                newFriendName: '',
                searchFriend: '',
                filterFriends: false
            }
        },
        computed: {
            myFriends() {
                const friends = this.$store.getters['chat/friends'];
                const searchFriend = this.searchFriend;
                return friends.filter(friend => {
                    return friend.friendUsername.includes(searchFriend);
                })
            }
        },
        methods: {
            sendFriendRequest() {
                if (this.newFriendName) {
                    this.$store.dispatch('chat/sendFriendRequest', this.newFriendName);
                    this.newFriendName = '';
                    this.$notify({
                        group: 'chat',
                        title: 'Friend request sent',
                        text: ''
                    });
                }
            }
        }
    }
</script>

<style scoped>
    .inbox_chat {
        height: calc(100% - 50px);
    }

    input:focus, button:focus {
        outline: none;
    }
</style>