<template>
    <div>
        <div class="incoming_msg" v-if="activeUser.friendId == message.msgFrom.id">
            <div class="received_msg">
                <div class="received_withd_msg">
                    <p>{{ message.toMsg }}</p>
                    <span class="time_date"> {{new Date(message.sendDate).toLocaleString()}}</span></div>
            </div>
        </div>
        <div class="outgoing_msg" v-else>
            <div class="sent_msg">
                <p>{{message.fromMsg}}.</p>
                <span class="time_date">{{new Date(message.sendDate).toLocaleString()}} </span></div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "MessageItem",
        props: ['message'],
        computed: {
            activeUser() {
                return this.$store.getters['chat/activeChat'];
            },
            authUser() {
                return this.$store.getters['security/userId'];
            }
        }
    }
</script>

<style scoped>

</style>