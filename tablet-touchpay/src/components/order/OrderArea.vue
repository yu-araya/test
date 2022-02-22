<template>
    <div class="order-area h100">
        <div class="card order-area__card h100">
            <header class="card-header order-area__card-header">
                <p class="card-header-title">注文内容</p>
            </header>
            <div class="card-content order-area__card-content">
                <div v-for="menuData in getMenuList" :key="menuData.food_division">
                    <OrderCard v-if="menuData.dispCard" :menuData="menuData"></OrderCard>
                </div>
            </div>
            <footer class="card-footer order-area__card-footer">
                <span class="card-footer-item">合計金額</span>
                <span class="card-footer-item" data-cy="totalCost">{{ getTotalCost | addComma }}円</span>
            </footer>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Mixins } from "vue-property-decorator";
import OrderCard from "@/components/order/OrderCard.vue";
import MixinComponent from "@/assets/js/mixin";
import { RequestData, MenuData, OrderData } from "@/store/types";
import { messages } from "@/assets/js/constant";

@Component({
    components: {
        OrderCard,
    },
})
export default class OrderArea extends Mixins(MixinComponent) {
    private key = "";

    created() {
        /**
         * IDm入力イベント
         */
        document.addEventListener("keypress", this.idmInputEvent);
    }

    get useQrcodeFlg(): boolean {
        return this.useQrcode;
    }

    get useQrcodeReaderDeviceFlg(): boolean {
        return this.useQrcodeReaderDevice;
    }


    private idmInputEvent(event: KeyboardEvent) {
        if (this.orderCountIsZero()) {
            this.createMessage(messages["info1"]);
            return;
        }
        if (!this.key) this.setLoading(true);
        if (event.which != 13) {
            this.key += event.key;
        } else {
            this.submit(this.key)
                .catch(async (err) => {
                    err = err instanceof Error ? messages["error1"] : err;
                    this.setLoading(false);
                    this.playSound("error");
                    this.createMessage(err);
                })
                .finally(() => {
                    this.key = "";
                });
        }
    }

    private async submit(key: string): Promise<void> {
        let employeeId = "";
            if(this.useQrcodeFlg && this.useQrcodeReaderDeviceFlg){
                employeeId = await this.decodeUserId(key);
            } else {
                employeeId = await this.getEmployeeIdWhenCorrectCard(key);
            }
        if (!employeeId) {
            throw messages["error2"];
        }

        await this.regist(employeeId);
    }

    private get getMenuList(): MenuData[] {
        return this.menuLists;
    }

    private get getTotalCost(): number {
        const menuLists = this.menuLists;
        let total = 0;
        menuLists.forEach((menuData) => {
            total += menuData.food_cost * menuData.count;
        });
        return total;
    }
}
</script>

<style lang="scss" scoped>
.order-area {
    &__card {
        border-radius: 6px;
        &-header {
            height: 10%;
        }
        &-content {
            height: 61.5vh;
            overflow: auto;
        }
        &-footer {
            height: 10%;
        }
    }
}
</style>
