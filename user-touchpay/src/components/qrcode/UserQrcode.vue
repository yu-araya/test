<template>
    <section class="container user-qrcode">
        <p class="user-qrcode__description">QRコードをタブレットで読み取ってください</p>
        <vue-qrcode class="user-qrcode__qr" :value="employeeId" :options="{ width: 300 }"></vue-qrcode>
        <div class="user-qrcode__link">
            <b-button type="is-primary" tag="router-link" :to="{ name: 'voucher' }" size="is-large" expanded>
                電子食券を表示
            </b-button>
        </div>
    </section>
</template>

<script lang="ts">
import { Component, Mixins } from "vue-property-decorator";
//eslint-disable-next-line
//@ts-ignore
import VueQrcode from "@chenfengyuan/vue-qrcode";
import StoreMixin from "@/store/StoreMixin";

@Component({
    components: {
        VueQrcode,
    },
})
export default class UserQrcode extends Mixins(StoreMixin) {
    get employeeId() {
        const base64userId = btoa(`userId${localStorage.userId}`);
        return base64userId;
    }
}
</script>

<style lang="scss">
.user-qrcode {
    display: flex;
    flex-direction: column;
    align-items: center;

    &__description {
        font-weight: bold;
    }

    &__qr {
        margin: 1em;
    }
}
</style>
