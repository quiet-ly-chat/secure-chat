<template>
    <div>
        <h3 class="section-title">Defined usernames</h3>
        <div class="list-box">
            <div class="list-header">
                <div style="flex:2; display: flex; flex-direction: column;">
                    <div class="display:flex;">
                        <span>Username &nbsp;
                            <i class="fas fa-filter" @click="startFilterByUsername"></i>&nbsp;&nbsp;
                            <i class="fas fa-sort" @click="sortUserNames"></i>
                        </span>
                    </div>
                    <div class="input-group mb-3" v-if="filterUsername">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input v-model="userNameFilterText" type="text" placeholder="Username" id="userNameFilterText"
                               class="form-control input_user"
                        />
                    </div>
                </div>


                <div style="flex:1; display: flex; flex-direction: column;">
                    <div style="display:flex; justify-content: center">
                        <span>Registered &nbsp;
                            <i class="fas fa-filter" @click="startFilterByStatus"></i>&nbsp;&nbsp;
                        </span>
                    </div>
                    <div class="input-group mb-3" v-if="filterStatus">
                        <true-false-switch v-model="statusToFilter"></true-false-switch>

                    </div>
                </div>
                <span style="flex:1; display: flex; justify-content: center">Remove</span>
            </div>
            <hr>
            <user-item v-for="user in users" :username="user.username" :active="!!user.user"
                       :key="user.username"></user-item>
        </div>
    </div>
</template>

<script>
    import UserItem from './UserItem';
    import TrueFalseSwitch from '../form/TrueFalseSwitch';

    export default {
        name: "UsernameList",
        components: {
            UserItem,
            TrueFalseSwitch
        },
        data() {
            return {
                allUsers: [],
                userNameOrder: 0,
                filterUsername: false,
                userNameFilterText: '',
                filterStatus: false,
                statusToFilter: -1
            }
        },
        created() {
            this.$store.dispatch('admin/getUsernames');
        },
        computed: {
            users() {
                let allUsers = this.$store.getters['admin/usernames'];

                if (this.filterUsername) {
                    allUsers = allUsers.filter(el => {
                        return el.username.match(this.userNameFilterText)
                    });
                }
                if (this.filterStatus && this.statusToFilter !== -1) {
                    allUsers = allUsers.filter(el => {
                        return this.statusToFilter === true && el.user != null || this.statusToFilter === false && el.user == null;
                    })
                }

                if (this.userNameOrder === 0) {
                    return allUsers;
                }
                if (this.userNameOrder > 0) {
                    allUsers.sort((a, b) => a.username.localeCompare(b.username));
                } else {
                    allUsers.sort((a, b) => b.username.localeCompare(a.username));
                }

                return allUsers;
            },
        },
        methods: {
            sortUserNames() {
                if (this.userNameOrder > 0) {
                    this.userNameOrder = -1;
                } else {
                    this.userNameOrder = 1;
                }
            },
            startFilterByUsername() {
                this.filterUsername = !this.filterUsername;
                if (!this.filterUsername) {
                    this.userNameFilterText = '';
                }
            },
            startFilterByStatus() {
                this.filterStatus = !this.filterStatus;
                if (!this.filterStatus) {
                    this.statusToFilter = -1;
                }
            },
        }
    }
</script>

<style scoped>
    .list-header {
        display: flex;
        justify-content: space-between;
        padding: 0 1.25rem 0.2rem;
        text-transform: uppercase;
        font-weight: bold;
    }

    .list-box {
        background-color: rgb(235, 235, 235);
        padding: 15px;
        margin: 0 -15px;
        border: 1px solid #3E8DA2;
    }

    .fas {
        padding: 5px;
    }
</style>


