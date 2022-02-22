<template>
    <div>
        <b-navbar class="is-dark" fixed-top>
            <template slot="brand">
                <b-navbar-item class="head-area__item" tag="router-link" :to="{ path: '/Order' }">
                    <img id="logo" class="head-area__icon" src="@/assets/img/agilecore_logo.png" alt="Agilecore_Logo" />
                </b-navbar-item>
            </template>
            <template slot="start">
                <b-navbar-item>注文画面 {{ version }}</b-navbar-item>
            </template>

            <template slot="end">
                <span class="head-area__failed-log">未送信件数: {{ getRecordCount }}件</span>
                <b-navbar-item class="head-area__item" @click="openSoldOutConf">
                    <b-icon id="soldout" icon="cogs" size="is-large" data-cy="soldOutSetting"></b-icon>
                </b-navbar-item>
                <b-navbar-item class="head-area__item" @click="reload">
                    <img id="reload" class="icon head-area__icon" src="@/assets/img/reload.svg" />
                </b-navbar-item>
            </template>
        </b-navbar>
        <div class="head-area__modal-area">
            <b-modal :active.sync="isSoldOutModalActive" width="60vw" scroll="keep">
                <SoldOut v-model="isSoldOutModalActive"></SoldOut>
            </b-modal>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Mixins } from "vue-property-decorator";
import MixinComponent from "@/assets/js/mixin";
import SoldOut from "@/components/order/SoldOut.vue";

@Component({
    components: { SoldOut },
})
export default class Header extends Mixins(MixinComponent) {
    async created() {
        this.setRecordCount(await this.db.allLogsCount());
    }
    private isSoldOutModalActive = false;

    private openSoldOutConf() {
        this.isSoldOutModalActive = true;
    }

    private reload() {
        this.initialize();
    }

    private get getRecordCount() {
        return this.recordCount;
    }

    private get version() {
        return localStorage.version;
    }
}
</script>

<style lang="scss" scoped>
.head-area {
    &__item {
        width: 75px;
        justify-content: center;
    }
    &__logo {
        transform: scale(1.5);
    }
    &__icon {
        transform: scale(1.5);
    }
    &__failed-log {
        margin: auto 10px;
    }
}
</style>
