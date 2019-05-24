<template>
    <div ref="messageDisplay" class="msg_history" v-if="activeUser"
         style="flex:7">
        <message-item v-for="(message, index) in messages" :message="message" :key="index"></message-item>
    </div>
</template>

<script>
    import MessageItem from './MessageItem';

    export default {
        name: "Messages",
        components: {
            MessageItem
        },
        data() {
            return {}
        },
        computed: {
            activeUser() {
                return this.$store.getters['chat/activeChat'];
            },
            messages() {
                return this.$store.getters['chat/messages'];
            }
        },
        updated() {
            let messageDisplay = this.$refs.messageDisplay;
            if (messageDisplay) {
                messageDisplay.scrollTop = messageDisplay.scrollHeight;
            }
        }
    }

</script>

<style scoped>

</style>