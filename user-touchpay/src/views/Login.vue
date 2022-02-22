<template>
    <section class="login">
        <ValidationObserver ref="observer" v-slot="{ passes }">
            <div class="box container login__form">
                <div class="tile is-vertical is-parent">
                    <div class="tile is-child login__form-title">ログイン</div>
                    <div class="tile is-child">
                        <BValidationInput
                            id="loginIdInput"
                            :rules="{ required: 'required', regex: /^[0-9a-zA-Z]+$/ }"
                            lavel="社員コード"
                            :horizontal="false"
                            v-model="id"
                        ></BValidationInput>
                    </div>
                    <div class="tile is-child">
                        <BValidationInput
                            id="loginPasswordInput"
                            :rules="{ required: 'required', regex: /^[0-9a-zA-Z]+$/ }"
                            lavel="パスワード"
                            type="password"
                            :horizontal="false"
                            v-model="password"
                        ></BValidationInput>
                    </div>
                    <div class="tile is-child">
                        <b-button id="loginButton" type="is-primary" expanded @click="passes(login)">ログイン</b-button>
                    </div>
                </div>
            </div>
        </ValidationObserver>
        <b-loading :is-full-page="true" :active.sync="isLoading" :can-cancel="false"> </b-loading>
    </section>
</template>

<script lang="ts">
// @ is an alias to /src
import { Component, Mixins } from "vue-property-decorator";
import StoreMixin from "@/store/StoreMixin";
import BValidationInput from "@/components/common/BValidationInput.vue";
import { showAlertDialog } from "@/assets/scripts/alert-dialog";
import { messages } from "@/assets/scripts/constant";

@Component({ components: { BValidationInput } })
export default class Login extends Mixins(StoreMixin) {
    private id = "";
    private password = "";

    private isLoading = false;

    private async created() {
        await this.getOptions().catch(() =>
            showAlertDialog(messages.error6.msg, messages.error6.type, () => window.location.reload())
        );
    }

    private async login() {
        this.isLoading = true;
        const loginResult = await this.loginConfirm(this.id, this.password);
        if (loginResult === "success") {
            await this.initialize();
            localStorage.userId = this.id;
            if (this.options.userReservationFlg) {
                this.$router.push({ name: "reservation", params: { userId: this.id, auth: "authenticated" } });
            } else {
                this.$router.push({ name: "foodHistory", params: { userId: this.id, auth: "authenticated" } });
            }
        } else if (loginResult === "failed") {
            showAlertDialog(messages.error1.msg, messages.error1.type);
        } else {
            showAlertDialog(messages.error5.msg, messages.error5.type);
        }
        this.isLoading = false;
    }
}
</script>

<style lang="scss" scoped>
@import "@/assets/style/global.scss";
.login {
    @media screen and (min-width: 640px) {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

    &__form {
        @media screen and (min-width: 640px) {
            width: 30vw;
        }

        &-title {
            font-size: 30px;
            font-weight: bold;
            border-bottom: 1px solid $THEME_COLOR;
            color: $THEME_COLOR;
        }
    }
}
</style>
