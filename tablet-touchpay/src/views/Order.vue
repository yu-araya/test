<template>
    <div class="order">
        <div class="order-menu">
            <div class="tile is-ancester order-menu__flex">
                <div class="tile is-parent is-8">
                    <div class="tile is-child box w100">
                        <MenuArea />
                    </div>
                </div>
                <div class="tile is-parent is-4">
                    <div class="tile is-child">
                        <OrderArea />
                    </div>
                </div>
            </div>
        </div>
        <div class="order-foot">
            <Footer />
        </div>
        <div class="order_message-area">
            <b-modal :active.sync="messageModal.isMessageModalActive" width="60vw" scroll="keep">
                <b-message :type="messageModal.messageType" full-screen has-icon aria-close-label="Close message">{{
                    messageModal.message
                }}</b-message>
            </b-modal>
        </div>
    </div>
</template>

<script lang="ts">
// @ is an alias to /src
import MenuArea from "@/components/order/MenuArea.vue";
import OrderArea from "@/components/order/OrderArea.vue";
import Footer from "@/components/order/Footer.vue";
import { Component, Mixins } from "vue-property-decorator";
import MixinComponent from "../assets/js/mixin";

@Component({
    components: {
        MenuArea,
        OrderArea,
        Footer,
    },
})
export default class Order extends Mixins(MixinComponent) {
    created() {
        this.getOption();
    }
}

</script>

<style lang="scss" scoped>
@import "@/assets/style/global.scss";
.order {
    &-menu {
        height: 80vh;
        &__flex {
            height: 100%;
        }
    }
}
</style>
