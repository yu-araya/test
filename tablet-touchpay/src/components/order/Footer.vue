<template>
    <!--<div class="foot-area h100">-->
    <b-navbar fixed-bottom type="is-light" class="foot-area">
        <template slot="start">
            <div class="tile is-parent foot-area__clear">
                <b-button class="is-child" @click="clear" type="is-light" size="is-medium" data-cy="selectClear">
                    選択を取り消し
                </b-button>
            </div>
        </template>
        <template slot="end">
            <div v-if="useQrcodeFlg">
                <div v-if="!useQrcodeReaderDeviceFlg">
                    <div class="tile is-parent" v-show="ordered">
                        <b-button
                            id="read-qrcode-button"
                            class="is-child"
                            size="is-medium"
                            type="is-primary"
                            @click="openQrModal"
                        >
                            QRコード読み込み
                        </b-button>
                    </div>
                </div>
                <div v-else>
                    <div class="tile is-parent" v-show="ordered">
                        <p class="is-child foot-area__message">2.QRコードを読取端末にタッチしてください</p>
                    </div>
                </div>
            </div>
            <div v-else>
                <div class="tile is-parent" v-show="ordered">
                    <p class="is-child foot-area__message">2.カードを読取端末にタッチしてください</p>
                </div>
            </div>
        </template>
    </b-navbar>
</template>

<script lang="ts">
import { Component, Mixins } from "vue-property-decorator";
import StoreMixin from "@/store/StoreMixin";
import ReadQrModal from "@/components/items/ReadQrModal.vue";

@Component({
    components: {
        ReadQrModal,
    },
})


@Component
export default class Footer extends Mixins(StoreMixin) {
    clear() {
        this.clearOrderData();
    }

    openQrModal() {
        this.$buefy.modal.open({
            parent: this,
            component: ReadQrModal,
        });
    }

    get useQrcodeFlg(): boolean {
        return this.useQrcode;
    }

    get useQrcodeReaderDeviceFlg(): boolean {
        return this.useQrcodeReaderDevice;
    }

    get ordered() {
        return this.isOrdered();
    }
}
</script>

<style lang="scss" scoped>
.foot-area {
    background-color: #cccccc;
    height: 10vh;

    &__message {
        font-weight: bold;
        font-size: 20px;
        padding: 5px 10px;
        border-radius: 6px;
        animation: message-color 0.5s infinite alternate;
    }

    @keyframes message-color {
        0% {
            background-color: red;
        }
        100% {
            background-color: yellow;
        }
    }
}
</style>
