<template>
    <div class="card w-100 mt-2" :class="{disabled: myUsername === username}">
        <div class="card-body" style="display: flex; justify-content: space-between">
            <span class="user-name">{{ username }}</span>
            <i v-if="active" class="user-icon far fa-check-circle"></i>
            <i v-else class="user-icon fas fa-times"></i>
            <i v-if="myUsername !== username" class="user-icon fas fa-trash" @click="removeUser"></i>
            <i class="user-icon" v-else></i>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'userItem',
        props: ['username', 'active'],
        methods: {
            removeUser() {
                if (confirm('Do you want to remove ' + this.username + '?')) {
                    this.$store.dispatch('admin/removeUsername', this.username);
                }
            }
        },
        computed: {
            myUsername() {
                return this.$store.getters['security/userName'];
            }
        }
    }
</script>
<style scoped>
    .user-icon {
        flex: 1;
        display: flex;
        justify-content: center;
    }

    .user-name {
        flex: 2;
    }

    .card.disabled {
        background-color: grey;
    }
</style>