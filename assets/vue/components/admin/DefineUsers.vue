<template>
    <div class="main-page">
        <div class="row col">
            <div class="container" style="margin-top: 20px">
                <div class="container"
                     style="display: flex; justify-content: center; align-items: center; margin-top: 20px">
                    <div class="user_card container"
                         style="padding: 50px; background: #FAFAFA; border: 1px solid #3E8DA2;">
                        <new-user></new-user>
                        <hr>
                        <div class="" style="margin: 0 50px">
                            <username-list></username-list>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import NewUser from './NewUser';
    import UsernameList from './UsernameList';

    export default {
        name: 'DefineUsers',
        components: {
            NewUser,
            UsernameList
        },
        created() {
            this.$store.dispatch('admin/getUsernames');
        },
        computed: {
            users() {
                return this.$store.getters['admin/usernames'];
            },
        }
    }
</script>