<template>
    <section class="login">
        <div class="box container login__form">
            <div class="tile is-vertical is-parent">
                <div class="tile is-child login__form-title">ログイン</div>
                <div class="tile is-child login__form-discript">ICタグをリーダーにタッチしてください</div>
                <div class="tile is-child">
                    <b-input></b-input>
                </div>
                <div class="tile is-child">
                    <b-button type="is-primary" expanded @click="login">ログイン</b-button>
                </div>
            </div>
        </div>
    </section>
</template>

<script lang="ts">
// @ is an alias to /src
import { Component, Mixins } from "vue-property-decorator";
import MixinComponent from "@/assets/js/mixin";

@Component
export default class Login extends Mixins(MixinComponent) {
    private login() {
        this.$router.push({ name: "Order" });
    }
}
</script>

<style lang="scss" scoped>
.login {
    &__form {
        width: 30vw;
        &-title {
            font-size: 30px;
            font-weight: bold;
            border-bottom: 1px solid #7957d5;
            color: #7957d5;
            font-family: "ヒラギノ丸ゴ ProN W4";
        }
    }
}
</style>
