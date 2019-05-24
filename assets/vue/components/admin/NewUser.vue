<template>
    <div>
        <h3 class="section-title">Add a new username</h3>
        <div class="d-flex justify-content-center form_container">
            <form @submit.prevent ref="form">
                <div class="input-group mb-3" :class="{invalid: $v.newUsername.$error}">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input v-model="newUsername" type="text" placeholder="Username" id="newUsername"
                           class="form-control input_user" name="_username"
                           required="required"
                           @blur="$v.newUsername.$touch()"
                    />
                </div>
                <p v-if="$v.newUsername.$error" class="invalid">
                    This field must not be empty.
                </p>

                <div class="d-flex justify-content-center mt-3 login_container">
                    <button @click="addUser()"
                            :disabled="newUsername.length === 0"
                            type="button" class="btn login_btn"
                            style="background-color: #EBEBEB; border: 1px solid #3E8DA2; color: black">
                        Add
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
    import {required} from 'vuelidate/lib/validators';

    export default {
        name: 'NewUser',
        data() {
            return {
                newUsername: '',
            };
        },
        methods: {
            addUser() {
                this.$store.dispatch('admin/addUsername', this.newUsername);
                this.$refs.form.reset();
            }
        },
        validations: {
            newUsername: {required}
        }
    }
</script>