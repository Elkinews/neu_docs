<template>
    <nde-form ref="form">
        <template v-slot:ndeFormBody>
            <v-row>
                <v-col cols="12">
                    <label>Current Password</label>
                    <v-text-field
                        v-model="old_pwd"
                        :append-icon="
                            showCurrentPassword ? 'mdi-eye' : 'mdi-eye-off'
                        "
                        :rules="[rules.requiredRule]"
                        :type="showCurrentPassword ? 'text' : 'password'"
                        :name="'currentPassword' + Math.random()"
                        hint="At least 8 characters"
                        counter
                        @click:append="
                            showCurrentPassword = !showCurrentPassword
                        "
                        aria-label="Current Password"
                        autocomplete="off"
                    ></v-text-field>
                </v-col>
                <v-col cols="12">
                    <label>New Password</label>
                    <v-text-field
                        v-model="new_pwd"
                        :append-icon="
                            showNewPassword ? 'mdi-eye' : 'mdi-eye-off'
                        "
                        :rules="rules.passwordRules"
                        :type="showNewPassword ? 'text' : 'password'"
                        :name="'showNewPassword' + Math.random()"
                        counter
                        @click:append="showNewPassword = !showNewPassword"
                        aria-label="New Password"
                        autocomplete="off"
                    ></v-text-field>
                </v-col>
                <v-col cols="12">
                    <label>Re-enter New Password</label>
                    <v-text-field
                        v-model="confirm_pwd"
                        :append-icon="
                            showReNewPassword ? 'mdi-eye' : 'mdi-eye-off'
                        "
                        :rules="[rules.requiredRule, rules.confirmPasswordRule]"
                        :type="showReNewPassword ? 'text' : 'password'"
                        :name="'showReNewPassword' + Math.random()"
                        counter
                        @click:append="showReNewPassword = !showReNewPassword"
                        aria-label="Re-enter New Password"
                        autocomplete="off"
                    ></v-text-field>
                </v-col>
            </v-row>
        </template>
    </nde-form>
</template>

<script>
import NdeForm from "./NdeForm.vue";
import {
  getFormPasswordRules,
  getRequiredRules,
  isValidPassword,
} from '../../utils/passwordValidator';

export default {
    name: "NdeFormSettingChangePassword",
    components: {
        NdeForm,
    },

    data() {
        return {
            showCurrentPassword: false,
            showNewPassword: false,
            showReNewPassword: false,
            validPassword: null,
            old_pwd: "",
            new_pwd: "",
            confirm_pwd: "",
            rules: {
                requiredRule: '',
                passwordRules: [],
                confirmPasswordRule: (value) => 
                    value === this.new_pwd || 'The password confirmation does not match.'
            },
        };
    },

    mounted() {
        this.rules.passwordRules = getFormPasswordRules();
        this.rules.requiredRule = getRequiredRules();
    },
    methods: {
        checkPassword(invalid) {
            invalid ? this.$emit('onUpdateValidPassword', false) : this.$emit('onUpdateValidPassword', true)
        }
    },
    watch: {
        new_pwd(newVal) {
            if (!newVal 
                || newVal !== this.confirm_pwd 
                || newVal === this.old_pwd
                || !isValidPassword(newVal)) {
               this.checkPassword(true)
               this.confirm_pwd = ''
            } else {
               this.checkPassword(false)
            }
        },

        confirm_pwd(newVal) {
            if (newVal !== this.new_pwd 
                || newVal === this.old_pwd 
                || !isValidPassword(newVal)
            ) {
               this.checkPassword(true)
            } else {
               this.checkPassword(false)
            }
        }
    }
};
</script>
<style scoped lang="scss">
</style>
