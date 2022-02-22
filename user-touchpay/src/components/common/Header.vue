<template>
    <div class="header h100">
        <b-navbar class="h100" type="is-dark">
            <template slot="brand">
                <b-navbar-item tag="div">
                    <img id="logo" class="header__logo" src="@/assets/img/agilecore_logo.png" alt="Agilecore_Logo" />
                </b-navbar-item>
            </template>

            <template slot="start">
                <span id="header-user-name" class="header__name" v-if="userInfo.id">{{ userInfo.name }}</span>
            </template>

            <template slot="end">
                <b-navbar-item
                    id="qrcode-nav"
                    v-if="userInfo.id && options.qrcodeFlg"
                    tag="router-link"
                    :to="{ name: 'qrcode' }"
                >
                    QRコード表示
                </b-navbar-item>
                <b-navbar-item
                    id="food-history-nav"
                    v-if="userInfo.id && options.userFoodHistoryFlg"
                    @click="pushNav('foodHistory')"
                >
                    喫食確認画面
                </b-navbar-item>
                <b-navbar-item
                    id="reservation-nav"
                    v-if="userInfo.id && options.userReservationFlg"
                    @click="pushNav('reservation')"
                >
                    予約画面
                </b-navbar-item>
                <b-navbar-item id="change-pass-nav" v-if="userInfo.id" @click="pushNav('password')">
                    パスワード変更
                </b-navbar-item>
                <b-navbar-item id="logout-nav" v-if="userInfo.id" @click="pushNav('login')">
                    ログアウト
                </b-navbar-item>
            </template>
        </b-navbar>
    </div>
</template>

<script lang="ts">
import { Component, Mixins } from "vue-property-decorator";
import StoreMixin from "@/store/StoreMixin";

Component.registerHooks(["beforeRouteUpdate"]);

@Component
export default class Header extends Mixins(StoreMixin) {
    private pushNav(name: string) {
        this.$router.push({ name });
    }
}
</script>

<style lang="scss">
.header {
    &__logo {
        margin-left: 10px;
    }

    &__name {
        margin: auto 10px;
    }
}
</style>
